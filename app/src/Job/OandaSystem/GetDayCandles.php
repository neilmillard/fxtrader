<?php

namespace App\Job\OandaSystem;

use App\Job;

class GetDayCandles extends Job\OandaSystem
{
    public function perform()
    {
        $this->oandaInfo->fetchDaily();

    }

    public function tearDown()
    {

    }

}
