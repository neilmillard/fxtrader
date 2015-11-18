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
use Resque;

final class AdminAction extends Controller
{

    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Profile page action dispatched");

        $this->view->render($response, 'admin.twig');
        return $response;
    }

    public function fetchCandlesDay(Request $request, Response $response, Array $args)
    {
        $job = 'App\Job\OandaSystem\GetDayCandles';
        $args = array(
            'time' => time(),
            'days' => '5',
        );
        $jobId = Resque::enqueue('default', $job, $args, true);

        $this->flash->addMessage('flash','Job Queued');
        return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
    }

}
