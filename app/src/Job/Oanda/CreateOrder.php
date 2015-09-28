<?php

namespace App\Job\Oanda;

use App\Job\Oanda;

/**
 * require Args array
    $args = array(
        'time' => time(),
        'userid' => '',
        'oanda' => [
            'accountId' => '',
            'order' => [
                'side'  => '',
                'pair'  => '',
                'price' => '',
                'expiry' => '',
                'stopLoss' => '',
                'takeProfit' => '',
                'risk'  => '',
            ]
        ],
    );

 */

class CreateOrder extends Oanda
{
    public function perform()
    {
        //TODO: place limit order job
        $side='buy';
        $pair='USD_CAD';
        $price='1.3500';
        $expiry=time()+60;
        $stopLoss='1.3400';
        $takeProfit=NULL;
        $risk=1;

        $this->oandaInfo->placeLimitOrder($side,$pair,$price,$expiry,$stopLoss,$takeProfit,$risk);

    }

    public function tearDown()
    {

    }

}
