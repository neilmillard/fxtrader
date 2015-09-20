<?php
// set the timezone
date_default_timezone_set('Europe/London');

// -----------------------------------------------------------------------------
// Service factories
// -----------------------------------------------------------------------------

// monolog
$container['logger'] = function ($c) {
    $settings = $c['settings']['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
    return $logger;
};

$container['dsn'] = function ($c) {
    $settings = $c['settings']['database'];
    $dsn = $settings['driver'] .
        ':host=' . $settings['host'] .
        ((!empty($settings['port'])) ? (';port=' . $settings['port']) : '') .
        ';dbname=' . $settings['database'];
    return $dsn;
};

$frozen = $container['settings']['database']['frozen'];
//with namespace Model
define( 'REDBEAN_MODEL_PREFIX', '\\App\\Model\\' );
\RedBeanPHP\R::setup($container['dsn'], $container['settings']['database']['username'], $container['settings']['database']['password'],$frozen);

// database mysqli connection
$container['database'] = function ($c) {
    $settings = $c['settings']['database'];
    $connection = new \PDO($c['dsn'],$settings['username'],$settings['password']);
    //$connection = new mysqli($settings['host'], $settings['username'], $settings['password'], $settings['database']);
    return $connection;
};

