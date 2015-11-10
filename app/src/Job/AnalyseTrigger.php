<?php

namespace App\Job;
use App\Job;
use RedBeanPHP\R;
use \Resque;
/**
 * TODO
 * require Args array
$args = array(
'time' => time(),
'analysisCandle' => 13447586, //timestamp of candle that triggered analysis
'gran' => 'D', //size of candle ^^^^^^^
'instrument' => 'USD_CAD',
'args' => [
 * dependant on signal
],

);

 */
class AnalyseTrigger extends Job
{
    /**
     * This job is created when a NEW candle is available and loaded from source.
     * Load strategies where instrument = instrument
     * for strategies as strategy{
     *   load args
     *
     *   create analyse job
     * }
     */
    public function perform()
    {
        $args = array(
            'time' => time(),
        );
        $args['instrument']     = $this->args['instrument'];
        $args['analysisCandle'] = $this->args['analysisCandle'];
        $args['gran']           = $this->args['gran'];

        $job = 'App\Job\Analyse';

        $this->logger->info("Processing Strategies for ".$args['instrument'].'@'.$this->args['time']);

        //load strategies
        $strategies = R::find('strategy',' instrument = :instrument', [':instrument' => $args['instrument']]);
        foreach ($strategies as $strategy) {
            $args['strategyId'] = $strategy->id;
            $args['signal']     = $strategy->signal;
            $args['params']     = $strategy->params;
            $jobId = Resque::enqueue('medium', $job, $args, true);
        }
        $delay = time() - $this->args['time'];
        $this->logger->info("Delay: '.$delay.' Seconds. Processed ".count($strategies)." Strategies");
        return;
    }
}