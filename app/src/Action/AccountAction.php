<?php

namespace App\Action;


use App\Broker\Broker_Oanda;
use RedBeanPHP\R;
use Slim\Http\Response;
use Slim\Http\Request;

class AccountAction extends Controller
{

    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Account page action dispatched");

        //grab identity id.
        $id=$this->authenticator->getIdentity();
        $user = R::load('users',$id['id']);
        $data = [];
        $data['accounts']=$user->ownAccountsList;
        $data['user']=$user;
        $this->view->render($response, 'accounts.twig',$data);
        return $response;
    }

    public function edit(Request $request, Response $response, Array $args)
    {
        $uid = $args['uid'];
        if(empty($uid)){
            $this->flash->addMessage('flash','No record specified');
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('accounts'));
        }
        $id=$this->authenticator->getIdentity();
        $user = R::load('users',$id['id']);
        if($uid!='new'){
            $account = R::load('accounts', $uid);
            if($account->id==0){
                $this->flash->addMessage('flash','No record found');
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('accounts'));
            }
            // restrict access to own profile or Admin role
            if($account->users->id!=$id['id']){
                if(strtolower($id['role'])!='admin'){
                    $this->flash->addMessage('flash','Access Denied');
                    return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('accounts'));
                }
            }
        } else {
            $account = R::dispense('accounts');
        }

        if ($request->isPost()) {
            $data = $request->getParams();
            $account->import($data,'apikey,accountid,servertype');
            $account->users = $user;
            $account->lasttid = 0;

            $oandaInfo = FALSE;
            // verify and get account balance
            try {
                $oandaInfo = new Broker_Oanda($account['servertype'], $account['apikey'], $account['accountid'], 0);
            } catch (\Exception $e){
                $viewData['flash']='Account Details Invalid';
            }

            if ($oandaInfo != FALSE) {
                $aid = R::store($account);
                $oandaInfo->updateAccount();
                $this->flash->addMessage('flash',"account updated");
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('editaccount',['uid'=>$aid]));
            }

        }
        $viewData['account']=$account;
        $this->view->render($response, 'account.twig',$viewData);
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
            throw new \Exception('Oanda AccountId not found');
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