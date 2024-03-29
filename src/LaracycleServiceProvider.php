<?php


namespace Laracycle;


use Illuminate\Support\ServiceProvider;
use Laracycle\Console\Commands\CycleMigrate;
use Laracycle\Console\Commands\CycleSchema;
use Laracycle\Providers\MigrationsServiceProvider;
use Laracycle\Providers\OrmServiceProvider;

class LaracycleServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->register(OrmServiceProvider::class);
        $this->app->register(MigrationsServiceProvider::class);

        $this->app->alias(Cycle::class, 'cycle');
    }

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/cycle.php' => config_path('cycle.php')
        ]);

        if ($this->app->runningInConsole()) {
            $this->commands([
                CycleSchema::class,
                CycleMigrate::class,
            ]);
        }
    }
}
