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

        //$this->oandaInfo->placeLimitOrder($side,$pair,$price,$expiry,$stopLoss,$takeProfit,$risk);

    }

    public function tearDown()
    {

    }

}
