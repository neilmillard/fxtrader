<?php
//if(empty($argv[1])) {
//    die('Specify the name of a job to add. e.g, php queue.php PHP_Job');
//}

$job = 'App\Job\Analyse';

require '../../vendor/autoload.php';
date_default_timezone_set('GMT');
require __DIR__ . '/../loadsettings.php';
$settings = loadsettings();

$REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
Resque::setBackend($REDIS_BACKEND);

$json = '{"time":1447609218,"instrument":"EUR_USD","analysisCandle":1447020000,"gran":"D","strategyId":"1","signal":"Flag","params":{"noOfPoleCandles":"3","maxBreatherCandles":"2","percentBreatherSize":".38","strongPoleCandleCent":".66","entryBufferPips":"0.0005"}}';
$args = json_decode($json, true);
$jobId = Resque::enqueue('default', $job, $args, true);
echo "Queued job " . $jobId . "\n\n";
