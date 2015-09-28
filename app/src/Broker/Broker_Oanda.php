<?php
namespace App\Broker;
use RedBeanPHP\R;

/**
 * Utility script to update values from oanda and load into database.
 * Uses config file for oanda api settings and oandawrap.php
 * pass number of days to retrieve on cmd e.g. getoandahist.php 40
 */
class Broker_Oanda {
    private $apiKey;
    private $accountId;
    private $serverType;
    private $pairs;
    private $oandaWrap;

    public function __construct($type, $apiKey, $accountId, $pairs=[]){
        $this->serverType = $type;
        $this->apiKey = $apiKey;
        $this->accountId = $accountId;
        if(isset($pairs[0])){
            $this->pairs = $pairs;
        } else {
            //setup default pairs
            $this->pairs     = [
                'USD_CAD', 'USD_CHF', 'USD_JPY',
                'AUD_USD', 'GBP_USD', 'NZD_USD',
                'EUR_USD', 'EUR_AUD', 'EUR_JPY',
                'AUD_JPY', 'GBP_JPY', 'AUD_NZD'
            ];
        }


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

    public function updateAccount(){
        $account = R::findOne('accounts',' accountid = ?', [ $this->accountId ]);
        if(empty($account)) {
            return;
        }
        $data = $this->oandaWrap->account($this->accountId);
        $account->balance = $data->balance;
        if($account->openTrades != $data->openTrades){
            // our trades have changed best get them updated
            // TODO: update trades table via a job
            $account->openTrades = $data->openTrades;
        }
        if($account->openOrders != $data->openOrders){
            // our ourders have changed best get them updated
            // TODO: update orders table via a job
            $account->openOrders = $data->openOrders;
        }
        $account->unrealizedPl = $data->unrealizedPl;
        $aid = R::store($account);

    }

    /**
     * This will get transactions from an account (from the last one acquired
     * and process the types to update orders and trades
     */
    public function processTransactions(){
        $account = R::findOne('accounts',' accountid = ?', [ $this->accountId ]);
        if(empty($account)) {
            return;
        }
        $transactionId = $account['lasttid'];
        //$transactions = $this->oandaWrap->transactions();
        $transactions = $this->oandaWrap->transactions_minid($transactionId);
        if(count($transactions>0)){
            foreach($transactions as $transaction){
                //check transaction type
                /*
                 * MARKET_ORDER_CREATE , STOP_ORDER_CREATE, LIMIT_ORDER_CREATE, MARKET_IF_TOUCHED_ORDER_CREATE,
                 * ORDER_UPDATE, ORDER_CANCEL, ORDER_FILLED,
                 * TRADE_UPDATE, TRADE_CLOSE,
                 * MIGRATE_TRADE_OPEN, MIGRATE_TRADE_CLOSE,
                 * STOP_LOSS_FILLED, TAKE_PROFIT_FILLED, TRAILING_STOP_FILLED,
                 * MARGIN_CALL_ENTER, MARGIN_CALL_EXIT, MARGIN_CLOSEOUT, SET_MARGIN_RATE,
                 * TRANSFER_FUNDS, DAILY_INTEREST, FEE
                 */
                switch ($transaction->type){
                    case 'ORDER_UPDATE':
                    case 'ORDER_CANCEL':
                        $currOrder = R::findOrCreate('orders',
                            ['oandaoid' => $transaction->orderId,
                                'instrument' => $transaction->instrument ]);
                        $currOrder->units = $transaction->units;
                        $currOrder->expiry = $transaction->expiry;
                        $currOrder->price = $transaction->price;
                        $currOrder->takeProfit = $transaction->takeProfit;
                        $currOrder->stopLoss = $transaction->stopLoss;
                        if($transaction->type=='ORDER_CANCEL'){
                            $currOrder->status='CANCELED';
                        }
                        R::store($currOrder);
                        unset($currOrder);
                        break;
                    case 'ORDER_FILLED':
                        $currOrder = R::findOrCreate('orders',
                            ['oandaoid' => $transaction->orderId,
                                'instrument' => $transaction->instrument ]);
                        $currOrder->status='FILLED';
                        $currTrade = R::findOrCreate('trades',
                            ['oandaoid' => $transaction->tradeId,
                                'instrument' => $transaction->instrument ]);
                        $currTrade->units=$transaction->units;
                        $currTrade->price = $transaction->price;
                        $currTrade->side = $transaction->side;
                        if(empty($currTrade->pl)){
                            $currTrade->pl = 0.00;
                        }
                        //$currTrade->takeProfit = $transaction->takeProfit;
                        //$currTrade->stopLoss = $transaction->stopLoss;
                        R::store($currTrade);
                        unset($currTrade);
                        break;
                    case 'TRADE_UPDATE':
                    case 'TRADE_CLOSE':
                    case 'STOP_LOSS_FILLED':
                    case 'TAKE_PROFIT_FILLED':
                        $currTrade = R::findOrCreate('trades',
                            ['oandaoid' => $transaction->tradeId,
                                'instrument' => $transaction->instrument ]);
                        $currTrade->units=$transaction->units;
                        $currTrade->price = $transaction->price;
                        $currTrade->side = $transaction->side;
                        $currTrade->pl = $transaction->pl;
                        R::store($currTrade);
                        unset($currTrade);
                        break;
                }

                if($transaction->id>$transactionId){
                    $transactionId=$transaction->id;
                }

            }
            $account['lasttid'] = $transactionId;
            R::store($account);
        }

    }

    public function updateOrders(){
        foreach($this->pairs as $pair){
            $orders = $this->oandaWrap->order_pair($pair);
            if (count($orders)>0) {
                foreach($orders as $order){
                    $currOrder = R::findOrCreate('orders',
                        ['oandaoid' => $order->id,
                         'instrument' => $order->instrument ]);
                    $currOrder->units = $order->units;
                    $currOrder->side = $order->side;
                    $currOrder->type = $order->type;
                    $currOrder->time = $order->time;
                    $currOrder->expiry = $order->expiry;
                    $currOrder->price = $order->price;
                    $currOrder->takeProfit = $order->takeProfit;
                    $currOrder->stopLoss = $order->stopLoss;
                    R::store($currOrder);
                    unset($currOrder);
                }
            }
        }
    }

    public function updateTrades(){
        foreach($this->pairs as $pair){
            $trades = $this->oandaWrap->trade_pair($pair);
            if (count($trades)>0) {
                foreach($trades as $trade) {
                    $currTrade = R::findOrCreate('trades',
                        ['oandatid' => $trade->id,
                        'instrument' => $trade->instrument ]);

                    $currTrade->unit=$trade->units;
                    $currTrade->side=$trade->side;
                    $currTrade->time=$trade->time;
                    $currTrade->price=$trade->price;
                    $currTrade->takeProfit=$trade->takeProfit;
                    $currTrade->stopLoss=$trade->stopLoss;
                    if(empty($currTrade->status)){
                        $currTrade->status='ORDER_FILLED';
                    }
                    R::store($currTrade);
                    unset($currTrade);
                }
            }
        }
    }

    /**
     * @param string    $side       'buy' or 'sell'
     * @param string    $pair       Name of Instrument
     * @param float     $price      execution price
     * @param int       $expiry     UTC timestamp format of order expiry e.g. now()+3600 (for 1 hour)
     * @param float     $stopLoss   Price of stopLoss
     * @param float     $takeProfit Price of takeProfit
     * @param int       $risk       Risk percent of account 1 = 1%
     */
    public function placeLimitOrder($side,$pair,$price,$expiry,$stopLoss,$takeProfit=NULL,$risk=1){
        //order options?
        $orderOptions = FALSE;
        //TODO calculate units based on risk
        // find how many pips risked
        $stopSize = (abs($price-$stopLoss))/$this->oandaWrap->instrument_pip($pair);
        // find risk amount of account
        $size = $this->oandaWrap->nav_size_percent($pair,$risk);
        $units = $size / $stopSize;

        /* @var \StdClass Oanda Order Object*/
        $order=$this->oandaWrap->order_open($side,$units,$pair,'limit',$price,$expiry,$orderOptions);
        // create and save order
        $currOrder = R::findOrCreate('orders',
            ['oandaoid' => $order->id,
                'instrument' => $order->instrument ]);
        $currOrder->units = $order->units;
        $currOrder->side = $order->side;
        $currOrder->type = $order->type;
        $currOrder->time = $order->time;
        $currOrder->expiry = $order->expiry;
        $currOrder->price = $order->price;

        // apply stop loss
        $sOrder=$this->oandaWrap->order_set_stop($currOrder['oandaoid'],$stopLoss);
        $currOrder->stopLoss = $stopLoss;
        // apply takeprofit if set
        if(!empty($takeProfit)){
            $tOrder=$this->oandaWrap->order_set_tp($currOrder['oandaoid'],$takeProfit);
            $currOrder->takeProfit = $order->takeProfit;
        }

        R::store($currOrder);

    }

    public function buy_bullish($pair, $risk, $stop, $leverage=50) {
        //Macro: Buy $pair and limit size to equal %NAV loss over $stop pips. Then set stopLoss

        //Retrieve current price
        if (! $this->valid($price = $this->price($pair)))
            return $price;

        //Find the correct size so that $risk is divided by $pips
        if (! $this->valid($size = $this->nav_size_percent_per_pip($pair, ($risk/$stop))))
            return $size;

        if (! $this->valid($newTrade = $this->buy_market($size, $pair)))
            return $newTrade;

        //Set the stoploss
        return $this->trade_set_stop($newTrade->tradeId, $price->ask + ($this->instrument_pip($pair) * $stop));
    }
}



