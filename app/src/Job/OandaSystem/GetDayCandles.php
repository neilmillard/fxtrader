<?php

namespace App\Job\OandaSystem;

use App\Job;
use Psr\Log\LogLevel;

class GetDayCandles extends Job\OandaSystem
{
    public function perform()
    {
        if (array_key_exists('candles', $this->args)) {
            $days = $this->args['candles'];
        } else {
            $days = 2;
        }
        $this->logger->info("Fetching ".$days.'Candles @'.$this->args['time']);
        $this->logger->log(
            LogLevel::INFO,
            'Fetching {days} Candles @{time}',
            array('days' => $days,
                'time' => $this->args['time'],
            )
        );
        $newCandles = $this->oandaInfo->fetchDaily($days);

        $this->runAnalysis($newCandles);

    }

    public function tearDown()
    {

    }

}
