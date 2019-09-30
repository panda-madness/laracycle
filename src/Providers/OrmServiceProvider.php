<?php

namespace Laracycle\Providers;


use Cycle\ORM\Factory;
use Cycle\ORM\ORM;
use Cycle\ORM\Schema;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Spiral\Database\Config\DatabaseConfig;
use Spiral\Database\DatabaseManager;
use Spiral\Tokenizer\ClassLocator;
use Symfony\Component\Finder\Finder;

class OrmServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(DatabaseManager::class, function() {
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
            $finder = (new Finder())->files()->in([ config('cycle.schema.path') ]);

            return new ClassLocator($finder);
        });
    }
}
