<?php

namespace App\Job;

use App\Job;
use RedBeanPHP\R;
use App\Broker\Broker_Oanda;

class Oanda extends Job
{
    /* @var Broker_Oanda */
    protected $oandaInfo;

    /**
     * require Args array
    $args = array(
    'time' => time(),
    'userid' => '',
    'oanda' => array(
    'accountId' => '',
    ),
    );

     */
    public function setUp()
    {
        parent::setUp();
        $accountId = $this->args['oanda']['accountId'];
        $account = R::findOne('accounts',' accountid = ?', [ $accountId ]);
        if(!empty($account)){
            $apiKey = $account['apikey'];
            $type = $account['servertype'];
            $this->oandaInfo = new Broker_Oanda($type, $apiKey,$accountId);
        } else {
            throw new \Exception('Oanda AccountId not found');
        }

    }
}
