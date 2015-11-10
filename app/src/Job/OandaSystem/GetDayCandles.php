<?php

namespace App\Job\OandaSystem;

use App\Job;
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

        $newCandles = $this->oandaInfo->fetchDaily($days);
        //TODO Trigger job if new candle(s)
        if(!empty($newCandles)){
            $job = 'App\Job\AnalyseTrigger';

            $this->container['logger']->info("Processing ".count($newCandles).'@'.$this->args['time']);

            $args = array(
                'time' => time(),
            );
            foreach ($newCandles as $newCandle) {
                $args['instrument']     = $newCandle['instrument'];
                $args['analysisCandle'] = $newCandle['analysisCandle'];
                $args['gran']           = $newCandle['gran'];

                $jobId = Resque::enqueue('medium', $job, $args, true);
                $this->container['logger']->info("Job ".$jobId.' for '.$newCandle['instrument']);
            }

        };


    }

    public function tearDown()
    {

    }

}
