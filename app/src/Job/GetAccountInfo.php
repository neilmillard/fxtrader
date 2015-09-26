<?php

namespace App\Job;

use App\Job;
use RedBeanPHP\R;
class GetAccountInfo extends Job
{
    /* @var \OandaWrap */
    private $oandaWrap;
    private $accountId;

    /**
     * require Args array
     $args = array(
        'time' => time(),
        'userid' => '',
        'oanda' => array(
            'apiKey' => '',
            'accountId' => '',
            'serverType' => '',
         ),
     );

     */
    public function setUp()
    {
        parent::setUp();
        $apiKey = $this->args['oanda']['apiKey'];
        $this->accountId = $accountId = $this->args['oanda']['accountId'];
        $type = $this->args['oanda']['serverType'];
        $oandaWrap = new \OandaWrap($type, $apiKey, $accountId, 0);
        if ($oandaWrap == FALSE) {
            throw new \Exception('Oanda Connection failed to initialize');
        }
        $this->oandaWrap = $oandaWrap;
    }

    public function perform()
    {

        $account = R::findOne('accounts',' accountid = ?', [ $this->accountId ]);
        if(!empty($account)) {
            $data = $this->oandaWrap->account($this->accountId);
            $account->balance = $data->balance;
            $account->openTrades = $data->openTrades;
            $account->openOrders = $data->openOrders;
            $account->unrealizedPl = $data->unrealizedPl;
            $aid = R::store($account);
        }

    }

    public function tearDown()
    {

    }

}
