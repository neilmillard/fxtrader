<?php

namespace App\Job\Oanda;

use App\Job\Oanda;

class GetAccountInfo extends Oanda
{
    public function perform()
    {

        $this->oandaInfo->updateAccount();
        //TODO: update orders and trades

    }

    public function tearDown()
    {

    }

}
