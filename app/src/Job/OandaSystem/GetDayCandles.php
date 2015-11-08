<?php

namespace App\Job\OandaSystem;

use App\Job;

class GetDayCandles extends Job\OandaSystem
{
    public function perform()
    {
        if (array_key_exists('days', $this->args)) {
            $days = $this->args['days'];
        } else {
            $days = 2;
        }

        $this->oandaInfo->fetchDaily($days);

    }

    public function tearDown()
    {

    }

}
