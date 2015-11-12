<?php

namespace App\Job\OandaSystem;

use App\Job;
use Psr\Log\LogLevel;
use \Resque;
class GetDayCandles extends Job\OandaSystem
{
    public function perform()
    {
        if (array_key_exists('days', $this->args)) {
            $days = $this->args['days'];
        } else {
            $days = 2;
        }
        $this->logger->info("Fetching ".$days.'Candles @'.$this->args['time']);

        $newCandles = $this->oandaInfo->fetchDaily($days);
        if(!empty($newCandles)){
            $job = 'App\Job\AnalyseTrigger';

            $this->logger->log(
                LogLevel::INFO,
                'Processing {candleCount} @ {time}',
                array('candleCount' => count($newCandles),
                    'time' => $this->args['time'],
                )
            );

            $args = array(
                'time' => time(),
            );
            foreach ($newCandles as $newCandle) {
                $args['instrument']     = $newCandle['instrument'];
                $args['analysisCandle'] = $newCandle['analysisCandle'];
                $args['gran']           = $newCandle['gran'];

                $jobId = Resque::enqueue('medium', $job, $args, true);
                $this->logger->log(
                    LogLevel::INFO,
                    'Queuing Job {jobid} for {instrument}',
                     array('jobid'    => $jobId,
                         'instrument' => $newCandle['instrument'],
                     )
                );
            }

        };


    }

    public function tearDown()
    {

    }

}
