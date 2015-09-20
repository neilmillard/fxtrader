<?php

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__ . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

require __DIR__ . '/../app/loadsettings.php';

$app = new \Slim\App($settings);

// DIC configuration
/**
 * @var \Slim\Container $container
 */
$container = $app->getContainer();

// Set up dependencies
require __DIR__ . '/../app/dependencies.php';

// Set up providers
require __DIR__ . '/../app/providers.php';

// Register middleware
require __DIR__ . '/../app/middleware.php';

// Register routes
require __DIR__ . '/../app/routes.php';

// Run!
$app->run();
