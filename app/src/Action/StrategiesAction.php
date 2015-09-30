<?php
namespace App\Action;

use RedBeanPHP\R;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Monolog\Logger;

final class StrategiesAction extends Controller
{

    public function admin(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Admin Strategies page action dispatched");
        $this->view->render($response, 'strategies.twig');
        return $response;
    }

    public function edit(Request $request, Response $response, Array $args)
    {
        $uid = $args['uid'];
        if(empty($uid)){
            $this->flash->addMessage('flash','No record specified');
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('homepage'));
        }
        $id=$this->authenticator->getIdentity();
        $user = R::load('users',$id['id']);
        if($uid!='new'){
            $strategy = R::load('strategies', $uid);
            if($strategy->id==0){
                $this->flash->addMessage('flash','No record found');
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('accounts'));
            }
            // restrict access to own profile or Admin role
            if(strtolower($id['role'])!='admin'){
                $this->flash->addMessage('flash','Access Denied');
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('accounts'));
            }

        } else {
            $strategy = R::dispense('strategies');
        }

        if ($request->isPost()) {
            $data = $request->getParams();
            $strategy->import($data,'name');

            $aid = R::store($strategy);
            $this->flash->addMessage('flash',"account updated");
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('editstrategy',['uid'=>$aid]));

        }
        $viewData['strategy']=$strategy;
        $this->view->render($response, 'account.twig',$viewData);
        return $response;

    }

}
