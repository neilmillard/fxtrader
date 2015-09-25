<?php
namespace App\Helper;
use RedBeanPHP\R;

/**
 * Utility script to update values from oanda and load into database.
 * Uses config file for oanda api settings and oandawrap.php
 * pass number of days to retrieve on cmd e.g. getoandahist.php 40
 */
class GetOandaHistory {
    private $apiKey;
    private $accountId;
    private $serverType;
    private $pairs;
    private $oandaWrap;

    public function __construct($apiKey, $accountId, $type, $pairs){
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
        $this->serverType = $type;
        $this->pairs = $pairs;

        //Check to see that OandaWrap is setup correctly.
        //Arg1 can be 'Demo', 'Live', or Sandbox;
        $oandaWrap = new \OandaWrap($type, $apiKey, $accountId, 0);
        if ($oandaWrap == FALSE) {
            throw new \Exception('Oanda Connection failed to initialize');
        }
        $this->oandaWrap = $oandaWrap;
        return;
    }

    public function fetchDaily($days = 2){

        $updated=0;
        $new=0;
        // offset so candle 'date' is more reflective. i.e. candle starts @ 2nd Jan 2015 @ 2200hr. That would be labeled as 2015-01-03.
        $dateOffsetSeconds = 60*60*12; // offset is 12 hrs as midpoint of daily
        foreach ($this->pairs as $pair){
            $start = mktime(0, 0, 1, date("m")  , date("d")-1, date("Y"));
            $start=$start-(($days)*(60*60*24));
            $history=$this->oandaWrap->candles($pair, 'D',array('start'=>$start, 'count'=>$days+1, 'candleFormat' => 'midpoint'));
            if(!isset($history->code)){
                $instrument = $history->instrument;
                foreach( $history->candles as $candle ) {
                    $date = date('Y-m-d', ($candle->time + $dateOffsetSeconds));
                    $candletime = $candle->time;
                    //get candle data so we can update it
                    $todayCandle = R::findOrCreate('candle',
                        [ 'date' => $date,
                          'instrument' => $instrument ]);
                    if($todayCandle->id)
                    {
                        $updated++;
                    } else {
                        $new++;
                    }
                    $todayCandle->candletime = $candletime;
                    $todayCandle->open = substr(sprintf("%.4f", $candle->openMid),0,6);
                    $todayCandle->high = substr(sprintf("%.4f", $candle->highMid),0,6);
                    $todayCandle->low = substr(sprintf("%.4f", $candle->lowMid),0,6);
                    $todayCandle->close = substr(sprintf("%.4f", $candle->closeMid),0,6);
                    $todayCandle->complete = $candle->complete;
                    $todayCandle->gran = 'D';
                    R::store($todayCandle);

                }
            }
        }
        echo date('Y-m-d H:i')." : ";
        echo "New:$new : Updated:$updated\n";

    }

    public function fetchHourly($hours = 2){

        $updated=0;
        $new=0;
        // offset so candle 'date' is more reflective. i.e. candle starts @ 2nd Jan 2015 @ 2200hr. That would be labeled as 2015-01-03.
        $dateOffsetSeconds = 0; // offset is 1 second as midpoint of hourly
        foreach ($this->pairs as $pair){
            $start = time();
            $start=$start-(($hours)*(60*60));
            $history=$this->oandaWrap->candles($pair, 'H1',array('start'=>$start, 'count'=>$hours+1, 'candleFormat' => 'midpoint'));
            if(!isset($history->code)){
                $instrument = $history->instrument;
                foreach( $history->candles as $candle ) {
                    $date = date('Y-m-d', ($candle->time + $dateOffsetSeconds));
                    $candletime = $candle->time;
                    //get candle data so we can update it
                    $todayCandle = R::findOrCreate('candle',
                        [ 'date' => $date,
                            'instrument' => $instrument ]);
                    if($todayCandle->id)
                    {
                        $updated++;
                    } else {
                        $new++;
                    }
                    $todayCandle->candletime = $candletime;
                    $todayCandle->open = substr(sprintf("%.4f", $candle->openMid),0,6);
                    $todayCandle->high = substr(sprintf("%.4f", $candle->highMid),0,6);
                    $todayCandle->low = substr(sprintf("%.4f", $candle->lowMid),0,6);
                    $todayCandle->close = substr(sprintf("%.4f", $candle->closeMid),0,6);
                    $todayCandle->complete = $candle->complete;
                    $todayCandle->gran = 'H1';
                    R::store($todayCandle);

                }
            }
        }
        echo date('Y-m-d H:i')." : ";
        echo "New:$new : Updated:$updated\n";

    }
}



