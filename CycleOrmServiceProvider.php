<?php


namespace PandaMadness\LaravelCycle;


use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;
use Spiral\Tokenizer\ClassLocator;
use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Symfony\Component\Finder\Finder;

class CycleOrmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseManager::class, function(Application $app) {
            $config = new DatabaseConfig(config('cycle.database'));
            return new DatabaseManager($config);
        });

        $this->app->singleton(ORM::class, function(Application $app) {
            $cache = $app->make(\Illuminate\Contracts\Cache\Factory::class)->store('file');

            if (!$cache->has('cycle.orm.schema')) {
                throw new \RuntimeException('Missing Cycle ORM Schema, did you run cycle:generate?');
            }

            $dbal = $app->make(DatabaseManager::class);
            $orm = new ORM(new Factory($dbal));
            $schema = new Schema($cache->get('cycle.orm.schema'));
            return $orm->withSchema($schema);
        });

        $this->app->singleton(ClassLocator::class, function() {
            $finder = (new Finder())->files()->in([ config('cycle.entities_directory') ]);

            return new ClassLocator($finder);
        });

        $this->app->alias(Cycle::class, 'cycle');

        $this->app->singleton(MigrationConfig::class, function(Application $app) {
            return new MigrationConfig(config('cycle.migrations'));
        });

        $this->app->singleton(Migrator::class, function(Application $app) {
            $dbal = $this->app->make(DatabaseManager::class);
            $config = $this->app->make(MigrationConfig::class);

            return new Migrator($config, $dbal, new FileRepository($config));
        });
    }

    public function boot()
    {

    }
}
