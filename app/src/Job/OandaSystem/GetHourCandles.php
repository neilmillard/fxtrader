<?php

namespace App\Job\OandaSystem;

use App\Job;

class GetHourCandles extends Job\OandaSystem
{

    public function perform()
    {
        $this->oandaInfo->fetchHourly();

    }

    public function tearDown()
    {

    }

}
