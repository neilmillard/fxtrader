<?php

namespace App\Job;

/**
 * TODO
 * require Args array
$args = array(
    'time' => time(),
    'AnalysisCandle' => timestamp of candle that triggered analysis
    'StrategyID' => '',
    'args' => [
        * dependant on signal
     ],

);

 */

class Analyse
{
    /**
     *   load candles (based on args and AnalysisCandle)
     *   construct signal class new Flag($args,$candles)
     *   run signal->analyse
     *   save recommendation (if any)
     *   trigger recommendation job
     */
}