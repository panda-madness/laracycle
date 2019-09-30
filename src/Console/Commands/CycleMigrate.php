<?php

namespace Laracycle\Console\Commands;

use Illuminate\Console\Command;
use Spiral\Migrations\Migrator;

class CycleMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cycle:migrate {direction}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations from the configured directory';
    /**
     * @var Migrator
     */
    private $migrator;

    /**
     * Create a new command instance.
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        parent::__construct();
        $this->migrator = $migrator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Throwable
     */
    public function handle()
    {
        $direction = $this->input->getArgument('direction');

        if (!$this->migrator->isConfigured()) {
            $this->migrator->configure();
        }

        if ($direction === 'up') {
            $this->runUp();
            return;
        }

        if ($direction === 'down') {
            $this->rollback();
            return;
        }

        return;
    }

    /**
     * @return int|void
     * @throws \Throwable
     */
    private function runUp() {
        while(($migration = $this->migrator->run()) !== null) {
            $this->info('Running migration ' . $migration->getState()->getName());
        }
    }

    /**
     * @throws \Throwable
     */
    private function rollback() {
        while(($migration = $this->migrator->rollback()) !== null) {
            $this->warn('Rolling back migration ' . $migration->getState()->getName());
        }
    }
}
