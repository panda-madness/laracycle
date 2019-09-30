<?php

return [
    'database' => [
        'default' => 'default',
        'databases' => [
            'default' => ['connection' => env('DB_CONNECTION', 'sqlite')],
        ],
        'connections' => [
            'sqlite' => [
                'driver' => \Spiral\Database\Driver\SQLite\SQLiteDriver::class,
                'connection' => 'sqlite:' . database_path('database.sqlite'),
                'username' => '',
                'password' => '',
            ],
            'mysql' => [
                'driver' => \Spiral\Database\Driver\MySQL\MySQLDriver::class,
                'options' => [
                    'connection' => 'mysql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('DB_DATABASE', 'laravel'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                ]
            ],
            'postgres' => [
                'driver' => \Spiral\Database\Driver\Postgres\PostgresDriver::class,
                'options' => [
                    'connection' => 'pgsql:host=' . env('DB_HOST', '127.0.0.1') . ';dbname=' . env('DB_DATABASE', 'laravel'),
                    'username' => env('DB_USERNAME', 'root'),
                    'password' => env('DB_PASSWORD', ''),
                ]
            ],
            'sqlServer' => [
                'driver'  => \Spiral\Database\Driver\SQLServer\SQLServerDriver::class,
                'options' => [
                    'connection' => 'sqlsrv:Server=OWNER;Database=DATABASE',
                    'username'   => 'sqlServer',
                    'password'   => 'sqlServer',
                ],
            ],
        ]
    ],
    'schema' => [
        'path' => app_path(),
    ],
    'migrations' => [
        'directory' => database_path('cycle_migrations'),
        'table' => 'cycle_migrations',
    ],
];
