<?php
/**
 * Setup Logger - called from resque and dependencies.php
 */

// monolog
$container['logger'] = function ($c) {
    $settings = $c['settings']['logger'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    if(empty($lsettings['loggly'])) {
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
    } else {
        $logger->pushHandler(new \Monolog\Handler\LogglyHandler($settings['loggly'].'/tag/monolog', \Monolog\Logger::INFO));
    }
    return $logger;
};