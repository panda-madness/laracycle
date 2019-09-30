<?php

namespace Laracycle\Console\Commands;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Illuminate\Console\Command;
use Illuminate\Contracts\Cache\Factory;
use Spiral\Database\DatabaseManager;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\Migrator;
use Spiral\Tokenizer\ClassLocator;
use Cycle\Schema;
use Cycle\Annotated;

class CycleSchema extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cycle:schema {--m|migrations : Create a migration file} {--s|sync : Sync tables to the generated schema}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and cache a cycle schema';
    /**
     * @var ClassLocator
     */
    private $classLocator;
    /**
     * @var DatabaseManager
     */
    private $dbal;
    /**
     * @var Factory
     */
    private $cacheFactory;
    /**
     * @var Migrator
     */
    private $migrator;
    /**
     * @var MigrationConfig
     */
    private $migrationConfig;

    /**
     * Create a new command instance.
     *
     * @param ClassLocator $classLocator
     * @param DatabaseManager $dbal
     * @param Factory $cacheFactory
     * @param Migrator $migrator
     * @param MigrationConfig $migrationConfig
     */
    public function __construct(
        ClassLocator $classLocator,
        DatabaseManager $dbal,
        Factory $cacheFactory,
        Migrator $migrator,
        MigrationConfig $migrationConfig
    )
    {
        parent::__construct();
        $this->classLocator = $classLocator;
        $this->dbal = $dbal;
        $this->cacheFactory = $cacheFactory;
        $this->migrator = $migrator;
        $this->migrationConfig = $migrationConfig;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrations = $this->input->getOption('migrations');
        $sync = $this->input->getOption('sync');

        if (!$this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        AnnotationRegistry::registerLoader('class_exists');

        $generators = [
            new Annotated\Embeddings($this->classLocator),            // register embeddable entities
            new Annotated\Entities($this->classLocator),              // register annotated entities
            new Schema\Generator\ResetTables(),       // re-declared table schemas (remove columns)
            new Schema\Generator\GenerateRelations(), // generate entity relations
            new Schema\Generator\ValidateEntities(),  // make sure all entity schemas are correct
            new Schema\Generator\RenderTables(),      // declare table schemas
            new Schema\Generator\RenderRelations(),   // declare relation keys and indexes
            new Schema\Generator\GenerateTypecast(),  // typecast non string columns
        ];

        if ($migrations) {
            $generators[] = new \Cycle\Migrations\GenerateMigrations($this->migrator->getRepository(), $this->migrationConfig);
        }

        if ($sync) {
            $generators[] = new Schema\Generator\SyncTables();
        }

        $schema = (new Schema\Compiler())->compile(new Schema\Registry($this->dbal), $generators);

        $cache = $this->cacheFactory->store('file');

        $cache->forever('cycle.orm.schema', $schema);

        return 0;
    }
}
