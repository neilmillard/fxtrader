<?php

// -----------------------------------------------------------------------------
// Service providers
// -----------------------------------------------------------------------------

// Twig
$view = new \Slim\Views\Twig(
    $app->settings['view']['template_path'],
    $app->settings['view']['twig']
);
$view->addExtension(new Twig_Extension_Debug());
$view->addExtension(new Slim\Views\TwigExtension(
    $container->get('router'),
    $container->get('request')->getUri()
));
/** @var Twig_Environment $twigEnvironment */
$twigEnvironment = $view->getEnvironment();
$twigEnvironment->addFilter(new Twig_SimpleFilter('ebase64', 'base64_encode'));
$twigEnvironment->addFilter(new Twig_SimpleFilter('dbase64', 'base64_decode'));

$container->register($view);

// Flash messages
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};

// authentication
$container['authenticator'] = function ($c) {
    $settings = $c['settings']['authenticator'];
    $connection = $c['database'];
    $adapter = new \App\Authentication\Adapter\Db\EloAdapter(
        $connection,
        $settings['tablename'],
        $settings['usernamefield'],
        $settings['credentialfield']
    );
    $authenticator = new \App\Authentication\Authenticator($adapter);
    return $authenticator;
};

// -----------------------------------------------------------------------------
// Action factories
// -----------------------------------------------------------------------------

$container['App\Action\HomeAction'] = function ($c) {
    return new App\Action\HomeAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\ProfileAction'] = function ($c) {
    return new App\Action\ProfileAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\AdminAction'] = function ($c) {
    return new App\Action\AdminAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\QueuesAction'] = function ($c) {
    return new App\Action\QueuesAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\UserAction'] = function ($c) {
    return new App\Action\UserAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\LoginAction'] = function ($c) {
    return new App\Action\LoginAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\AccountAction'] = function ($c) {
    return new App\Action\AccountAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\StrategiesAction'] = function ($c) {
    return new App\Action\StrategiesAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};

$container['App\Action\TestAction'] = function ($c) {
    return new App\Action\TestAction($c['view'], $c['logger'], $c['router'], $c['flash'], $c['authenticator']);
};