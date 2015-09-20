<?php
namespace App\Action;

use App\Authentication\Authenticator;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Monolog\Logger;

final class LoginAction extends Controller
{
    public function login(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Login page action dispatched");
        $username = null;
        $error = null;

        $urlRedirect = $request->getUri()->getBaseUrl().$this->router->pathFor('homepage');

//        if ($request->getAttribute('r') && $request->getAttribute('r') != '/logout' && $request->getAttribute('r') != '/login') {
//            $_SESSION['urlRedirect'] = $request->getAttribute('r');
//        }

        if (isset($_SESSION['urlRedirect'])) {
            $urlRedirect = $_SESSION['urlRedirect'];
            unset($_SESSION['urlRedirect']);
        }

        if ($request->isPost()) {
            $username = $request->getParam('username');
            $password = $request->getParam('password');

            $result = $this->authenticator->authenticate($username, $password);

            if ($result->isValid()){
                //$error = $this->authenticator->getIdentity();
                return $response->withRedirect($urlRedirect);
            } else {
                $messages = $result->getMessages();
                $error=(string) $messages[0];
                //$this->flash->addMessage('flash', $error);

            }
        }
        $this->view->render($response, 'login.twig',['username'=> $username,
                                                     'error'=> $error]);
        return $response;
    }

    public function logout(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Logout request action");
        $this->authenticator->clearIdentity();
        return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('homepage'));
    }
}
