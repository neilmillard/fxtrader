
<?php
/**
 * Launch resque scheduler workers
 * User: Neil
 * Date: 21/09/2015
 * Time: 09:17
 */

require __DIR__ . '/../../../vendor/autoload.php';

date_default_timezone_set('Europe/London');

require __DIR__ . '/../../loadsettings.php';
$settings = loadsettings();

$REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
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

$interval = 5;
$INTERVAL = $settings['resque']['INTERVAL'];
if(!empty($INTERVAL)) {
    $interval = $INTERVAL;
}


$worker = new ResqueScheduler_Worker();
$worker->logLevel = $logLevel;

$PIDFILE = $settings['resquescheduler']['PIDFILE'];
if ($PIDFILE) {
    file_put_contents($PIDFILE, getmypid()) or
    die('Could not write PID information to ' . $PIDFILE);
}

fwrite(STDOUT, '*** Starting scheduler worker '."\n");
$worker->work($interval);
