<?php

namespace App\Action;


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

            // verify and get account balance
            $oandaWrap = new \OandaWrap($account['servertype'], $account['apikey'], $account['accountid'], 0);
            if ($oandaWrap == FALSE) {
                $viewdata['flash']='Account Details Invalid';
            } else {
                $data=$oandaWrap->account($account['accountid']);
                $account->balance=$data->balance;
                $account->openTrades=$data->openTrades;
                $account->openOrders=$data->openOrders;
                $account->unrealizedPl=$data->unrealizedPl;
                $aid = R::store($account);
                $this->flash->addMessage('flash',"account updated");
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('editaccount',['uid'=>$aid]));
            }

        }
        $viewdata['account']=$account;
        $this->view->render($response, 'account.twig',$viewdata);
        return $response;

    }
}