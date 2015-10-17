<?php

namespace App\Signals;
use App\Signal;

/**
 * Class Flag
 * looks for the flag in the supplied candles and returns a recommendation
 * @package App\Signals
 */
class Flag extends Signal
{
    public $name = 'The Flag';
    public $description =   'This signal will find a flag based on 2 or 3 candles in the pole with 1 breather candle '.
                            'with two % settings for body/range of pole candles and % of breather retracement';
    protected $reqNumCandles = 10;

    /**
     * Shows a list of argument names for this signal
     * @return array
     */
    public static function showArgs(){
        $argsNames = [
            'noOfPoleCandles',
            'maxBreatherCandles',
            'percentBreatherSize',
            'strongPoleCandleCent',
            'entryBufferPips'
        ];

        return $argsNames;

    }

    /**
     * sets the arguments. accepts an array key=>value pairs
     * @param array $args
     * @return int
     * @throws \Exception
     */

    public function setArgs(Array $args){
        if($this->checkArgs($args)){
            $this->args=$args;
            $this->reqNumCandles = $this->args['noOfPoleCandles']+$this->args['maxBreatherCandles']+2;
            return count($args);
        } else {
            return 0;
        }
    }

    /**
     * Returns an array key-value of the current arguments
     * @return array
     */
    public function getArgs(){
        return $this->args;
    }

    /**
     * Loads the candles into the instance, [date,instrument,candletime,open,high,low,close,complete,gran]
     * @param array $candles
     * @return int
     */
    public function loadCandles(Array $candles)
    {
        if (count($candles) > $this->getReqNumCandles()) {
            $this->candles = array_reverse($candles);
            $this->analyseCandles = true;
            return (count($candles));
        } else {
            return 0;
        }
    }

    /**
     * Returns the min number of Candles required for this Signal
     * @return int
     */
    public function getReqNumCandles()
    {
        return $this->reqNumCandles;
    }

    /**
     * Runs the analysis and returns a recommendation.
     * @return Array $recommendation
     * [BOOL trade, instrument, side, entry, stopLoss, rr]
     */
    public function analyse(){
        // we need a min of 5 candles for this pattern
        $values = [];
        $trade = false;
        $values['trade'] = $trade;
        $values['instrument'] = '';
        $values['side'] = '';
        $values['entry'] = '';
        $values['stopLoss'] = '';
        $values['rr'] = 1;
        $values['gran'] = 'D';
        $values['expiry'] = 0;

        // first find if we need 2 or three in our flag pole
        $polecandles = $this->args['noOfPoleCandles'];
        // TODO this code only checks for 1 breather candle!
        $maxBreatherCandles = $this->args['maxBreatherCandles'];
        $breatherCent = $this->args['percentBreatherSize'];
        $strong = $this->args['strongPoleCandleCent'];
        $buffer = $this->args['entryBufferPips'];
        $maxValid = $maxBreatherCandles;
        for($i=0;$i<$maxValid;$i++){
            // find pattern  ------------------------------------------------------------------
            $pattern = false;
            if (($this->direction($i + 1) == $this->direction($i + 2)) && ($this->direction($i) != $this->direction($i + 1))) {
                if($polecandles==2) {
                    $pattern = true;
                } elseif($polecandles==3){
                    if($this->direction($i+2)==$this->direction($i+3)){
                        $pattern = true;
                    }
                }
            }
            if (!$pattern)
                continue;

            // check size of breather  ---------------------------------------------------------
            if( ($this->range($i) / $this->totalRange($i+1,$i+1+$polecandles)) > $breatherCent)
                continue;

            // check pole is strong  -----------------------------------------------------------
            $strongPoles = true;
            for($j=1;$j<$polecandles;$j++){
                $perCentBody = $this->body($j) / $this->range($j);
                if($perCentBody<$strong)
                    $strongPoles = false;
            }

            if (!$strongPoles)
                continue;

            // does the breather breach the pole?
            if($this->poleBreach($i))
                continue;

            // probably got a valid signal.
            //$validPatternShift = $i;
            $breatherDirection = $this->direction($i);
            $trade = true;

            if($breatherDirection=='BEAR'){
                $values['entry'] = max($this->high($i),$this->high($i+1))+$buffer;
                $values['stopLoss'] = $this->low($i) - $buffer;
                $values['side'] = 'buy';
            }
            if($breatherDirection=='BULL'){
                $values['entry'] = min($this->low($i),$this->low($i+1))-$buffer;
                $values['stopLoss'] = $this->high($i) + $buffer;
                $values['side'] = 'sell';
            }

            break;
        }

        if($trade){
            $values['trade'] = $trade;
            $values['instrument'] = $this->candles[0]['instrument'];
            $values['rr'] = 1;
            $values['gran'] = $this->candles[0]['gran'];
            $granTime = \OandaWrap::gran_seconds($values['gran']);
            $values['expiry'] = time() + $granTime;
        }

        return $values;
    }

    /**
     * @param $shift
     * @return bool
     */
    protected function poleBreach($shift){
        if($this->direction($shift)=='BEAR'){
            return ($this->high($shift)>$this->high($shift+1));
        } else {
            return ($this->low($shift)<$this->low($shift+1));
        }
    }

}