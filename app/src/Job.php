<?php
/**
 * Created by PhpStorm.
 * User: Neil
 * Date: 20/09/2015
 * Time: 21:21
 */

namespace App;

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

    public function setUp()
    {
        // load the $settings
        require_once __DIR__ . '/../loadsettings.php';
        $settings = loadsettings();
        $this->settings = $settings;
        $setup['settings'] = $settings;
        $container = new Container($setup);
        $this->container = $container;
        // Set up dependencies
        require_once __DIR__ . '/../dependencies.php';
    }

}