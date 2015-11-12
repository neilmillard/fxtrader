<?php

namespace App;

use Psr\Log\LoggerInterface;
use Slim\Container;

class Job
{

    /* @var array */
    protected $settings;
    /* @var Container */
    protected $container;
    /* @var \Resque_Job */
    public $job;
    /* @var array */
    public $args;
    /* @var string The name of the queue that this job belongs to. */
    public $queue;
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
        $REDIS_BACKEND = $this->settings['resque']['REDIS_BACKEND'];
        \Resque::setBackend($REDIS_BACKEND);
        $this->logger = $this->job->worker->logger;
    }

}