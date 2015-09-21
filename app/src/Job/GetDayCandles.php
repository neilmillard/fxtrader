<?php

namespace App\Job;

use App\Helper\GetOandaHistory;
use App\Job;

class GetDayCandles extends Job
{
    /* @var GetOandaHistory */
    private $oandaHistory;

    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->settings['oanda']['apiKey'];
        $accountId = $this->settings['oanda']['accountId'];
        $pairs = $this->settings['oanda']['pairs'];
        $this->oandaHistory = new GetOandaHistory($apiKey,$accountId, $pairs);
    }

    public function perform()
    {
        $this->oandaHistory->fetchDaily();

    }

    public function tearDown()
    {

    }

}
