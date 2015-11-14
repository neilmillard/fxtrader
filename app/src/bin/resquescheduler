#!/usr/bin/env php56
<?php
use Monolog\Handler\LogglyHandler;

/**
 * Launch resque scheduler workers
 * User: Neil
 * Date: 21/09/2015
 * Time: 09:17
 */


error_reporting(E_ERROR | E_WARNING | E_PARSE);





require __DIR__ . '/../../../vendor/autoload.php';

date_default_timezone_set('Europe/London');

require __DIR__ . '/../../loadsettings.php';
$settings = loadsettings();

$REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
// A redis database number
$REDIS_BACKEND_DB = getenv('REDIS_BACKEND_DB');
if(!empty($REDIS_BACKEND)) {
    if (empty($REDIS_BACKEND_DB))
        Resque::setBackend($REDIS_BACKEND);
    else
        Resque::setBackend($REDIS_BACKEND, $REDIS_BACKEND_DB);
}

$logLevel = 0;
$LOGGING = $settings['resque']['LOGGING'];
$VERBOSE = $settings['resque']['VERBOSE'];
$VVERBOSE = $settings['resque']['VVERBOSE'];
if(!empty($LOGGING) || !empty($VERBOSE)) {
    $logLevel = ResqueScheduler_Worker::LOG_NORMAL;
}
else if(!empty($VVERBOSE)) {
    $logLevel = ResqueScheduler_Worker::LOG_VERBOSE;
}

$lsettings = $settings['logger'];
$logger = new \Monolog\Logger($lsettings['name']);
$logger->pushProcessor(new \Monolog\Processor\UidProcessor());
if (empty($lsettings['loggly'])) {
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($lsettings['path'], \Monolog\Logger::DEBUG));
} else {
    $logger->pushHandler(new LogglyHandler($lsettings['loggly'] . '/tag/fxschedworker', \Monolog\Logger::INFO));
}

$interval = 5;
$INTERVAL = $settings['resque']['INTERVAL'];
if(!empty($INTERVAL)) {
    $interval = $INTERVAL;
}

$worker = new ResqueScheduler_Worker();
$worker->setLogger($logger);
$worker->logLevel = $logLevel;

//$PIDFILE = $settings['resquescheduler']['PIDFILE'];
$PIDFILE = getenv('PIDFILE');
if ($PIDFILE) {
    file_put_contents($PIDFILE, getmypid()) or
    die('Could not write PID information to ' . $PIDFILE);
}

//check schedules
ResqueScheduler::reloadSchedules();
$schedules = ResqueScheduler::schedules();
if (empty($schedules)) {
    // Instantiate the app
    $path = __DIR__ . '/../../../app/schedules.php';
    if (file_exists($path)) {
        $schedule = require $path;
        ResqueScheduler::schedule($schedule);
    }
} else {
    //clean for testing
    ResqueScheduler::cleanSchedules();
}

$logger->log(Psr\Log\LogLevel::NOTICE, 'Starting scheduler worker {worker}', array('worker' => $worker));
$worker->work($interval);
