<?php
//if(empty($argv[1])) {
//    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
//}

$job = 'App\Job\GetDayCandles';
$job = 'App\Job\GetAccountInfo';

require '../../vendor/chrisboulton/php-resque/lib/Resque.php';
require '../../vendor/chrisboulton/php-resque-scheduler/lib/ResqueScheduler.php';

date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$in = 3; //one hour
//$args = array(
//    'time' => time(),
//    'array' => array(
//        'test' => 'test',
//    ),
//);

$args = array(
    'time' => time(),
    'userid' => 'not needed',
    'oanda' => array(
        'apiKey' => '',
        'accountId' => '',
        'serverType' => 'Demo',
    ),
);

ResqueScheduler::enqueueIn($in, 'default', $job, $args);

//$jobId = Resque::enqueue('default', $job, $args, true);
echo "Queued job \n\n";
