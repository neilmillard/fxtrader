<?php

namespace App\Job;

use App\Helper\GetOandaHistory;
use App\Job;

class GetHourCandles extends Job
{
    /* @var GetOandaHistory */
    private $oandaHistory;

    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->settings['oanda']['apiKey'];
        $accountId = $this->settings['oanda']['accountId'];
        $type = $this->settings['oanda']['serverType'];
        $pairs = $this->settings['oanda']['pairs'];
        $this->oandaHistory = new GetOandaHistory($apiKey,$accountId, $type, $pairs);
    }

    public function perform()
    {
        $this->oandaHistory->fetchHourly();

    }

    public function tearDown()
    {

    }

}
