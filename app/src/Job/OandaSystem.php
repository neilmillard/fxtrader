<?php

namespace App\Job;

use App\Job;
use App\Helper\GetOandaInfo;

class OandaSystem extends Job
{
    /* @var GetOandaInfo */
    protected $oandaInfo;

    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->settings['oanda']['apiKey'];
        $accountId = $this->settings['oanda']['accountId'];
        $type = $this->settings['oanda']['serverType'];
        $pairs = $this->settings['oanda']['pairs'];
        $this->oandaInfo = new GetOandaInfo($type, $apiKey,$accountId, $pairs);

    }
}
