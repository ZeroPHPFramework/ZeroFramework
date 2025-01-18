<?php

return [
    'connection' => 'sqlite',

    'mysql' => [
        'driver' => 'mysql',
        'host' =>  env('MYSQL_HOST', '127.0.0.1'),
        'port' => env('MYSQL_PORT', '3306'),
        'database' => env('MYSQL_DATABASE', 'zero'),
        'username' => env('MYSQL_USER', 'root'),
        'password' => env('MYSQL_PASSWORD', ''),
    ],
    'sqlite' => [
        'driver' => 'sqlite3',
        'database' => env('SQLITE_DATABASE', base('sqlite/zero.sqlite')),
    ]
];