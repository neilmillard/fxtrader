<?php
//if(empty($argv[1])) {
//    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
//}

$job = 'App\Job\OandaSystem\GetDayCandles';

require '../../vendor/autoload.php';
date_default_timezone_set('GMT');
require __DIR__ . '/../loadsettings.php';
$settings = loadsettings();

$REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
Resque::setBackend($REDIS_BACKEND);

$args = array(
    'time' => time(),
    'userid' => 'not needed',
    'days' => '200',
    'oanda' => array(
        'accountId' => '6717454',
    ),
);
$jobId = Resque::enqueue('default', $job, $args, true);
echo "Queued job ".$jobId."\n\n";
?>