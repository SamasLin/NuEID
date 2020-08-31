<?php
declare(strict_types=1);

return [
    'database' => [
        'master' => [
            'host' => '127.0.0.1',
            'port' => 8889,
            'type' => 'mysql',
            'dbname' => 'crud',
            'user' => 'root',
            'password' => 'root'
        ],
        'slave' => [
            'host' => '127.0.0.1',
            'port' => 8889,
            'type' => 'mysql',
            'dbname' => 'crud',
            'user' => 'root',
            'password' => 'root'
        ]
    ]
];
