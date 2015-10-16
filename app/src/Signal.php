<?php

namespace App;


abstract class Signal
{

    protected $argsnames = [];
    protected $args;
    protected $candles;


    /**
     * @param $args
     * @param $candles
     * @throws \Exception
     */
    public final function __construct($args,$candles){
        if($this->setArgs($args)==0){
            throw new \Exception('Not enough args');
        }
        if($this->loadCandles($candles)==0){
            throw new \Exception('Not enough Candles');
        };
        return $this;
    }
    /**
     * Shows a list of argument names for this signal
     * @return array
     */
    public static function showArgs(){
        return ['Not Configured'];
    }

    protected function checkArgs(Array $args){
        $okay= true;
        $argNames = $this->showArgs();
        foreach($argNames as $argName){
            if(!isset($args[$argName])){
                $okay=false;
            }
        }
        return $okay;
    }

    /**
     * sets the arguments. accepts an array key=>value pairs
     * @param array $args
     * @return int
     */
    public function setArgs(Array $args){
        if($this->checkArgs($args)){
            $this->args=$args;
            return count($args);
        } else {
            return 0;
        }
    }

    /**
     * Returns an array key-value of the current arguments
     * @return array
     */
    abstract public function getArgs();

    /**
     * Returns the min number of Candles required for this Signal
     * @return int
     */
    abstract public function getReqNumCandles();

    /**
     * Loads the candles into the instance, [date,instrument,candletime,open,high,low,close,complete,gran]
     * @param array $candles
     * @return int
     */
    abstract public function loadCandles(Array $candles);

    /**
     * Runs the analysis and returns a recommendation.
     * @return Array $recommendation
     * [instrument, side, open, stopLoss, stopLossPips, rr]
     */
    abstract public function analyse();

    protected function direction($shift){
        if($this->open($shift)>$this->close($shift)){
            return 'BEAR';
        } else {
            return 'BULL';
        }
    }

    /**
     * return the range of a candle
     * @param $shift
     * @return float
     */
    protected function range($shift){
        return abs($this->high($shift)-$this->low($shift));

    }

    /**
     * Find the total range of two candles
     * @param $shift1
     * @param $shift2
     * @return mixed
     */
    protected function totalRange($shift1,$shift2){
        return max($this->high($shift1)-$this->low($shift2),$this->high($shift2)-$this->low($shift1));
    }

    protected function body($shift){
        return abs($this->open($shift)-$this->close($shift));
    }

    /**
     * @param $shift
     * @return float
     */
    protected function high($shift){
        $high = $this->candles[$shift]['high'];
        return $high;
    }

    /**
     * @param $shift
     * @return float
     */
    protected function low($shift){
        $low = $this->candles[$shift]['low'];
        return $low;
    }

    protected function open($shift){
        $open = $this->candles[$shift]['open'];
        return $open;
    }

    protected function close($shift){
        $close = $this->candles[$shift]['close'];
        return $close;
    }
}