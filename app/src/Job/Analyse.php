<?php

namespace App\Job;
use App\Job;

//$args = array(
//    'time'           => time(),
//    'instrument'     => 'USD_CAD',
//    'analysisCandle' => 3948085858, //timestamp of candle that triggered analysis
//    'gran'           => 'D',
//    'strategyId'     => 3,
//    'signal'         => 'Flag',
//    'params'         => [], //dependant on signal
//);


class Analyse extends Job
{
    /**
     *   load candles (based on args and AnalysisCandle)
     *   construct signal class new Flag($args,$candles)
     *   run signal->analyse
     *   save recommendation (if any)
     *   trigger recommendation job
     */
    public function perform()
    {
        $instrument = $this->args['instrument'];


        // Check keys exist in args.
        $order = $this->args['oanda']['order'];



    }
}