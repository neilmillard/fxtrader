<?php

namespace App\Job;

use App\Job;
use App\Broker\Broker_Oanda;

class OandaSystem extends Job
{
    /* @var Broker_Oanda */
    protected $oandaInfo;

    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->settings['oanda']['apiKey'];
        $accountId = $this->settings['oanda']['accountId'];
        $type = $this->settings['oanda']['serverType'];
        $pairs = $this->settings['oanda']['pairs'];
        $this->oandaInfo = new Broker_Oanda($type, $apiKey,$accountId, $pairs);

    }
}
