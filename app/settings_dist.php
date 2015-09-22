<?php
return [

    // View settings
    'view' => [
        'template_path' => __DIR__ . '/templates',
        'twig' => [
            'cache' => __DIR__ . '/../cache/twig',
            'debug' => true,
            'auto_reload' => true,
        ],
    ],

    // monolog settings
    'logger' => [
        'name' => 'app',
        'path' => __DIR__ . '/../log/app.log',
    ],

    // database settings
    'database' => [
        'driver'    => 'mysql',
        'host'      => 'localhost',
        'database'  => 'database',
        'username'  => 'username',
        'password'  => 'Pas5wrd',
        'charset'   => 'utf8',
        'collation' => 'utf8_general_ci',
        'prefix'    => '',
        'frozen'    => true,
    ],

    // authentication settings
    'authenticator' => [
        'tablename' => 'users',
        'usernamefield' => 'email',
        'credentialfield' => 'hash'
    ],

    //oanda root account settings
    'oanda' => [
        'apiKey'    => 'yourapikey',
        'accountId' => 'youraccid',
        'serverType'=> 'Demo',
        'pairs'     => [
            'USD_CAD', 'USD_CHF', 'USD_JPY',
            'AUD_USD', 'GBP_USD', 'NZD_USD',
            'EUR_USD', 'EUR_AUD', 'EUR_JPY',
            'AUD_JPY', 'GBP_JPY', 'AUD_NZD'
        ]
    ],

    // Resque
    'resque'    => [
        'REDIS_BACKEND' => '127.0.0.1:6379',
        'LOGGING'       => '',
        'VERBOSE'       => '',
        'VVERBOSE'      => '',
        'INTERVAL'      => 5,
        'COUNT'         => 1,
        'PIDFILE'       => '',

    ]
];
