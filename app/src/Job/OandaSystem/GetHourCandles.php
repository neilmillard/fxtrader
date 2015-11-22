<?php

namespace App\Job\OandaSystem;

use App\Job;
use Psr\Log\LogLevel;
class GetHourCandles extends Job\OandaSystem
{

    public function perform()
    {
        if (array_key_exists('candles', $this->args)) {
            $candles = $this->args['candles'];
        } else {
            $candles = 2;
        }
        $this->logger->log(
            LogLevel::NOTICE,
            'Fetching {candles} Candles @{time}',
            array('candles' => $candles,
                'time' => $this->args['time'],
            )
        );
        $newCandles = $this->oandaInfo->fetchHourly($candles);

        $this->runAnalysis($newCandles);


    }

    public function tearDown()
    {

    }

}
