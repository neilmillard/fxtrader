<?php

namespace App\Job;

use App\Job;
use RedBeanPHP\R;
use App\Helper\GetOandaInfo;

class Oanda extends Job
{
    /* @var GetOandaInfo */
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
            $this->oandaInfo = new GetOandaInfo($type, $apiKey,$accountId);
        } else {
            throw new \Exception('Oanda AccountId not found');
        }

    }
}
