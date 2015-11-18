<?php

namespace App\Job;
use App\Job;
use App\Models\Recommendations as Model_Recommendations;
use RedBeanPHP\R;

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
        $gran       = $this->args['gran'];
        $endtime    = $this->args['analysisCandle'];
        // help SQLSTATE[HY000]: General error: 2006 MySQL server has gone away?
        R::testConnection();
        $class = $this->args['signal'];
        //full namespace to signals
        $signalClass = 'App\\Signals\\'.$class;
        if (class_exists($signalClass))
        {
            /* @var \App\Signal $signalTest */
            $signalTest = new $signalClass($this->args['params'], array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));
            $noCandles = $signalTest->getReqNumCandles();
        } else {
            throw new \Exception("Signal $signalClass not found");
        }
        unset($signalTest);
        //min number of candles required by the signal
        //$noCandles       = 10;
        $candleBeans = R::find(
            'candle',
            ' instrument = :instrument AND gran = :gran AND candletime <= :endTime ORDER BY date DESC LIMIT :candles',
            [
                ':instrument' => $instrument,
                ':gran' => $gran,
                ':candles' => $noCandles,
                ':endTime'  => $endtime
            ]
        );
        if(count($candleBeans)==$noCandles){
            $candles = R::exportAll($candleBeans);
        } else {
            throw new \Exception("Not enough or no candles found for $instrument before $endtime");
        }
        /* @var \App\Signal $analysisClass */
        $analysisClass = new $signalClass($this->args['params'],$candles);
        $result = $analysisClass->analyse();
        if($result['trade']){
            $recommendation = new Model_Recommendations($result);
            $recommendation->setStrategy( $this->args['strategyId'] );
            $this->logger->info("Recommendation found: for ".$instrument." by StratID:".$this->args['strategyId']);
            //TODO: trigger orders

        }


    }
}