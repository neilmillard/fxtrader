<?php
namespace App\Action;

use App\Authentication\Authenticator;
use RedBeanPHP\R;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Monolog\Logger;

final class ProfileAction extends Controller
{
    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Profile page action dispatched");

        //grab identity id.
        $id=$this->authenticator->getIdentity();
        $user = R::findOne('users',' email = :username ',['username'=>$id['email']]);
        $expUser = $user->export();
        $this->view->render($response, 'profile.twig',$expUser);
        return $response;
    }

    public function editUser(Request $request, Response $response, Array $args)
    {
        $username = $args['username'];
        if(empty($username)){
            $this->flash->addMessage('flash','No user specified');
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('profile'));
        }
        $username = base64_decode($username);
        $id=$this->authenticator->getIdentity();
        // restrict access to own profile or Admin role
        if($username!=strtolower($id['email'])){
            if(strtolower($id['role'])!='admin'){
                $this->flash->addMessage('flash','Access Denied');
                return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('profile'));
            }
        }
        $user = R::findOne('users', ' email = ? ', [$username]);
        if ($user == NULL) {
            $user = R::dispense('users');
        }
        if ($request->isPost()) {
            $data = $request->getParams();
            //$username = $request->getParam('username');
            $user->import($data,'userfullname');
            $user->email = $request->getParam('username');
            $password = $request->getParam('userpassword');
            if(!empty($password)){
                $pass = password_hash($password, PASSWORD_DEFAULT);
                $user->hash = $pass;
            }

            $id = R::store($user);
            $this->flash->addMessage('flash',"$user->name updated");
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('edituser',['username'=>base64_encode($username)]));
        }
        $expUser['user']= $user->export();
        $expUser['user']['hashemail'] = base64_encode($user['email']);
        $this->view->render($response, 'user.twig',$expUser);
        return $response;

    }
}
