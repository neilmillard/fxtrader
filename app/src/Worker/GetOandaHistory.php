<?php
namespace App\Worker;
use RedBeanPHP\R;

/**
 * Utility script to update values from oanda and load into database.
 * Uses config file for oanda api settings and oandawrap.php
 * pass number of days to retrieve on cmd e.g. getoandahist.php 40
 */
class GetOandaHistory {
    private $apiKey;
    private $accountId;
    private $pairs;

    public function __construct($apiKey, $accountId, $pairs){
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;

        //Check to see that OandaWrap is setup correctly.
        //Arg1 can be 'Demo', 'Live', or Sandbox;
        if (\OandaWrap::setup('Demo', $apiKey, $accountId, 0) == FALSE) {
            echo 'OandaWrap failed to initialize, ';
            echo 'contact will.whitty.arbeit@gmail.com to submit a bug report.';
            exit(1);
        }

        return;
    }

    public function fetch($days = 2){

        $updated=0;
        $new=0;
        // offset so candle 'date' is more reflective. i.e. candle starts @ 2nd Jan 2015 @ 2200hr. That would be labeled as 2015-01-03.
        $dateOffsetSeconds = 60*60*12; // offset is 12 hrs as midpoint of daily
        foreach ($this->pairs as $pair){
            $start = mktime(0, 0, 1, date("m")  , date("d")-1, date("Y"));
            $start=$start-(($days)*(60*60*24));
            $history=\OandaWrap::candles($pair, 'D',array('start'=>$start, 'count'=>$days+1, 'candleFormat' => 'midpoint'));
            if(!isset($history->code)){
                $instrument = $history->instrument;
                foreach( $history->candles as $candle ) {
                    $date = date('Y-m-d', ($candle->time + $dateOffsetSeconds));
                    $candletime = $candle->time;
                    //get candle data so we can update it
                    $todayscandle = R::findOrCreate('candle',
                        [ 'date' => $date,
                          'instrument' => $instrument ]);
                    if($todayscandle->id)
                    {
                        $updated++;
                    } else {
                        $new++;
                    }
                    $todayscandle->candletime = $candletime;
                    $todayscandle->open = substr(sprintf("%.4f", $candle->openMid),0,6);
                    $todayscandle->high = substr(sprintf("%.4f", $candle->highMid),0,6);
                    $todayscandle->low = substr(sprintf("%.4f", $candle->lowMid),0,6);
                    $todayscandle->close = substr(sprintf("%.4f", $candle->closeMid),0,6);
                    $todayscandle->complete = $candle->complete;

                    R::store($todayscandle);

                }
            }
        }
        echo date('Y-m-d H:i')." : ";
        echo "New:$new : Updated:$updated\n";

    }
}



