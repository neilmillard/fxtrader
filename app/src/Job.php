<?php

namespace App;

use Psr\Log\LoggerInterface;
use RedBeanPHP\R;
use Slim\Container;
use Psr\Log\LogLevel;
use \Resque;

class Job
{

    /* @var \Resque_Job */
    public $job;
    /* @var array */
    public $args;
    /* @var string The name of the queue that this job belongs to. */
    public $queue;
    /* @var array */
    protected $settings;
    /* @var Container */
    protected $container;
    /* @var LoggerInterface */
    protected $logger;

    public function setUp()
    {
        // load the $settings
        require_once __DIR__ . '/../loadsettings.php';
        $settings = loadsettings();
        $this->settings = $settings;
        $setup['settings'] = $settings;
        $container = new Container($setup);
        $this->container = $container;
        // Set up datalayer
        require_once __DIR__ . '/../datalayer.php';
//  moved to datalayer.php
//        $REDIS_BACKEND = $this->settings['resque']['REDIS_BACKEND'];
//        \Resque::setBackend($REDIS_BACKEND);
        $this->logger = $this->job->worker->logger;
    }

    public function tearDown()
    {
        R::close();
    }

    public function runAnalysis($newCandles)
    {
        if (!empty($newCandles)) {
            $job = 'App\Job\AnalyseTrigger';

            $this->logger->log(
                LogLevel::INFO,
                'Processing {candleCount} @ {time}',
                array('candleCount' => count($newCandles),
                    'time' => $this->args['time'],
                )
            );

            $args = array(
                'time' => time(),
            );
            foreach ($newCandles as $newCandle) {
                $args['instrument'] = $newCandle['instrument'];
                $args['analysisCandle'] = $newCandle['analysisCandle'];
                $args['gran'] = $newCandle['gran'];

                $jobId = Resque::enqueue('medium', $job, $args, true);
                $this->logger->log(
                    LogLevel::INFO,
                    'Queuing Job {jobid} for {instrument}',
                    array('jobid' => $jobId,
                        'instrument' => $newCandle['instrument'],
                    )
                );
            }

        };
    }

}