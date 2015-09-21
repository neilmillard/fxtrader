
<?php
/**
 * Launch resque workers
 * User: Neil
 * Date: 21/09/2015
 * Time: 09:17
 */

/*
error_reporting(E_ERROR | E_WARNING | E_PARSE);
if(empty($argv[1])) {
    die('Specify the name or list of queues(comma separated list), php resque.php files');
}

$QUEUE = $argv[1];
*/
$QUEUE = '*';

require __DIR__ . '/../../../vendor/autoload.php';

date_default_timezone_set('Europe/London');

require __DIR__ . '/../../loadsettings.php';

$REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];

if(!empty($REDIS_BACKEND)) {
    Resque::setBackend($REDIS_BACKEND);
}

$logLevel = 0;
$LOGGING = $settings['resque']['LOGGING'];
$VERBOSE = $settings['resque']['VERBOSE'];
$VVERBOSE = $settings['resque']['VVERBOSE'];
if(!empty($LOGGING) || !empty($VERBOSE)) {
    $logLevel = Resque_Worker::LOG_NORMAL;
}
else if(!empty($VVERBOSE)) {
    $logLevel = Resque_Worker::LOG_VERBOSE;
}

$interval = 5;
$INTERVAL = $settings['resque']['INTERVAL'];
if(!empty($INTERVAL)) {
    $interval = $INTERVAL;
}

$count = 1;
$COUNT = $settings['resque']['COUNT'];
if(!empty($COUNT) && $COUNT > 1) {
    $count = $COUNT;
}

if($count > 1) {
    for($i = 0; $i < $count; ++$i) {
        $pid = pcntl_fork();
        if($pid == -1) {
            die("Could not fork worker ".$i."\n");
        }
        // Child, start the worker
        else if(!$pid) {
            $queues = explode(',', $QUEUE);
            $worker = new Resque_Worker($queues);
            $worker->logLevel = $logLevel;
            fwrite(STDOUT, '*** Starting worker '.$worker."\n");
            $worker->work($interval);
            break;
        }
    }
}
// Start a single worker
else {
    $queues = explode(',', $QUEUE);
    $worker = new Resque_Worker($queues);
    $worker->logLevel = $logLevel;

    $PIDFILE = $settings['resque']['PIDFILE'];
    if ($PIDFILE) {
        file_put_contents($PIDFILE, getmypid()) or
        die('Could not write PID information to ' . $PIDFILE);
    }

    fwrite(STDOUT, '*** Starting worker '.$worker."\n");
    $worker->work($interval);
}
