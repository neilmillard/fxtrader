<?php

namespace App\Job;

use App\Helper\GetOandaInfo;
use App\Job;

class GetDayCandles extends Job
{
    /* @var GetOandaInfo */
    private $oandaInfo;

    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->settings['oanda']['apiKey'];
        $accountId = $this->settings['oanda']['accountId'];
        $type = $this->settings['oanda']['serverType'];
        $pairs = $this->settings['oanda']['pairs'];
        $this->oandaInfo = new GetOandaInfo($apiKey,$accountId, $type, $pairs);
    }

    public function perform()
    {
        $this->oandaInfo->fetchDaily();

    }

    public function tearDown()
    {

    }

}
