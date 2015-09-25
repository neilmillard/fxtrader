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

    protected $settings;
    protected $container;

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