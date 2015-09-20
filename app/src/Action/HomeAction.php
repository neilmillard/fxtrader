<?php
namespace App\Action;

use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Monolog\Logger;

final class HomeAction extends Controller
{

    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Home page action dispatched");
        $this->view->render($response, 'home.twig');
        return $response;
    }
}
