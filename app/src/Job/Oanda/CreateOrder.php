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
        // Check keys exist in args.
        $order = $this->args['oanda']['order'];

        $side = $order['side'];
        $pair = $order['pair'];
        // TODO check price is valid with side
        $price = $order['price'];
        $expiry = $order['expiry'];
        $stopLoss = $order['stopLoss'];
        $takeProfit = $order['takeProfit'];
        $risk = $order['risk'];

        $this->oandaInfo->placeLimitOrder($side,$pair,$price,$expiry,$stopLoss,$takeProfit,$risk);

    }

    public function tearDown()
    {

    }

}
