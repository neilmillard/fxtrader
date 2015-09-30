<?php

namespace App\Action;


use App\Broker\Broker_Oanda;
use RedBeanPHP\R;
use Slim\Http\Response;
use Slim\Http\Request;

class TestAction extends Controller
{

    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Test page action dispatched");

        return $response;
    }

    public function test(Request $request, Response $response, Array $args){
        $uid = $args['uid'];
        $myaccount = R::load('accounts', $uid);
        $accountId = $myaccount->accountid;
        $account = R::findOne('accounts',' accountid = ?', [ $accountId ]);
        if(!empty($account)){
            $apiKey = $account['apikey'];
            $type = $account['servertype'];
            $oandaInfo = new Broker_Oanda($type, $apiKey,$accountId);
        } else {
            $this->flash->addMessage('flash',"Oanda AccountId not found");
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('homepage'));
        }

        $side='buy';
        $pair='EUR_USD';
        $price='1.1400';
        $expiry = time()+60;
        $stopLoss='1.1300';
        $takeProfit=NULL;
        $risk=1;

//        $side='buy';
//        $pair='GBP_CHF';
//        $price='2.1443';
//        $expiry = $oandaInfo->getExpiry(time()+60);
//        $stopLoss='2.1452';
//        $takeProfit=NULL;
//        $risk=1;

        //$oandaInfo->placeLimitOrder($side,$pair,$price,$expiry,$stopLoss,$takeProfit,$risk);

        $oandaInfo->processTransactions();

    }
}