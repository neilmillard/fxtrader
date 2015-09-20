<?php
// Routes

$app->get('/', 'App\Action\HomeAction:dispatch')
    ->setName('homepage');

/** @noinspection PhpUndefinedMethodInspection */
$app->get('/profile', 'App\Action\ProfileAction:dispatch')
    ->setName('profile')
    ->add('Authenticator\Middleware:auth');

/** @noinspection PhpUndefinedMethodInspection */
$app->map(['GET','POST'],'/edituser/{username}', 'App\Action\ProfileAction:edituser')
    ->setName('edituser')
    ->add('Authenticator\Middleware:auth');

/** @noinspection PhpUndefinedMethodInspection */
$app->get('/admin', 'App\Action\AdminAction:dispatch')
    ->setName('admin')
    ->add('Authenticator\Middleware:auth');

/** @noinspection PhpUndefinedMethodInspection */
$app->get('/users', 'App\Action\UserAction:dispatch')
    ->setName('users')
    ->add('Authenticator\Middleware:auth');


$app->map(['GET', 'POST'], '/login', 'App\Action\LoginAction:login')
    ->setName('login');

$app->get('/logout', 'App\Action\LoginAction:logout')
    ->setName('logout');
