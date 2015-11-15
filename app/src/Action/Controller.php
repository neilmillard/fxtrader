<?php
namespace App\Action;

use App\Authentication\Authenticator;
use Slim\Flash\Messages;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Router;
use Slim\Views\Twig;
use Monolog\Logger;

abstract class Controller
{
    protected $view;
    protected $logger;
    protected $router;
    protected $authenticator;
    protected $flash;

    public function __construct(Twig $view, Logger $logger, Router $router, Messages $flash, Authenticator $authenticator)
    {
        $this->view = $view;
        $this->logger = $logger;
        $this->router = $router;
        $this->authenticator = $authenticator;
        $this->flash = $flash;
    }

    public function dispatch(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Controller dispatched");
        //$this->view->render($response, 'home.twig');
        return $response;
    }

    /**
     * get the instruments - at the moment only oanda is supported
     * @return mixed
     */
    public function getInstruments($broker = 'oanda')
    {
        //$instruments = [];
        $settings = loadsettings();
        $instruments = (array_key_exists($broker, $settings) ? $settings[$broker]['pairs'] : []);
        return $instruments;
    }
}