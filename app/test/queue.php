<?php
//if(empty($argv[1])) {
//    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
//}

$job = 'App\Job\GetHourCandles';

require '../../vendor/chrisboulton/php-resque/lib/Resque.php';
date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$args = array(
    'time' => time(),
    'array' => array(
        'test' => 'test',
    ),
);

$jobId = Resque::enqueue('default', $job, $args, true);
echo "Queued job ".$jobId."\n\n";
?>