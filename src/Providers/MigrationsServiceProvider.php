<?php


namespace Laracycle\Providers;


use Carbon\Laravel\ServiceProvider;
use Illuminate\Foundation\Application;
use Spiral\Database\DatabaseManager;
use Spiral\Migrations\Config\MigrationConfig;
use Spiral\Migrations\FileRepository;
use Spiral\Migrations\Migrator;

class MigrationsServiceProvider extends ServiceProvider
{
    public function register()
    {
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
