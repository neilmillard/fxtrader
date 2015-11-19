<?php
namespace App\Action;

use Psr\Log\LogLevel;
use RedBeanPHP\R;
use Slim\Http\Request;
use Slim\Http\Response;
use \Resque;

final class StrategiesAction extends Controller
{

    public function admin(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Admin Strategies page action dispatched");
        $strategies = R::findAll( 'strategies' );

        $this->view->render($response, 'strategies.twig',['strategies'=> $strategies]);
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
                return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
            }
            // restrict access to own profile or Admin role
            if(strtolower($id['role'])!='admin'){
                $this->flash->addMessage('flash','Access Denied');
                return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
            }

        } else {
            $strategy = R::dispense('strategies');
        }

        if ($request->isPost()) {
            $data = $request->getParams();
            $strategy->import($data,'name, description, strategyexit, signal, instrument');

            $aid = R::store($strategy);
            $this->flash->addMessage('flash',"Strategy updated");
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));

        }
        $viewData['strategy']=$strategy;
        $signalList=$this->getSignals();
        $viewData['signallist']=$signalList;
        $signals = [];
        foreach($signalList as $signal){
            $signals[$signal['name']]=json_decode($signal['argNames']);
        }
        $viewData['signallistjson']=json_encode($signals);
        $viewData['instruments']=$this->getInstruments();
        $this->view->render($response, 'strategy.twig',$viewData);
        return $response;

    }

    public function getSignals()
    {
        $signals = [];
        foreach (glob(__DIR__ . '/../Signals/*.php') as $file) {
            // get the file name of the current file without the extension
            // which is essentially the class name
            $class = basename($file, '.php');
            //full namespace to signals
            $testClass = 'App\\Signals\\' . $class;
            if (class_exists($testClass)) {
                $argNames = call_user_func($testClass . '::showArgs');
                $argNames = json_encode($argNames);
                $signals[] = ['name' => $class,
                    'argNames' => $argNames
                ];
            }
        }
        return $signals;
    }

    public function options(Request $request, Response $response, Array $args)
    {
        $uid = $args['uid'];
        if (empty($uid)) {
            $this->flash->addMessage('flash', 'No record specified');
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('homepage'));
        }
        $id = $this->authenticator->getIdentity();
        $user = R::load('users', $id['id']);
        $strategy = R::load('strategies', $uid);
        if ($strategy->id == 0) {
            $this->flash->addMessage('flash', 'No record found');
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('stratagies'));
        }
        // restrict access to own profile or Admin role
        if (strtolower($id['role']) != 'admin') {
            $this->flash->addMessage('flash', 'Access Denied');
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('stratagies'));
        }

        $params = $this->getParams($strategy);

        if ($request->isPost()) {
            $data = $request->getParams();
            $options = [];
            foreach ($data as $key => $value) {
                if (!$params || ($params && in_array($key, $params))) {
                    $options[$key] = $value;
                }
            }
            $strategy->params = $options;

            $aid = R::store($strategy);
            $this->flash->addMessage('flash', "Strategy updated");
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('editstrategy', ['uid' => $aid]));

        }
        $viewData['strategy'] = $strategy;
        $viewData['params'] = $params;
        $this->view->render($response, 'strategyoptions.twig', $viewData);
        return $response;

    }

    public function getParams($strategy){
        $params=[];
        $class = $strategy->signal;
        //full namespace to signals
        $testClass = 'App\\Signals\\'.$class;
        if (class_exists($testClass))
        {
            $params = call_user_func($testClass.'::showArgs');
        }
        return $params;
    }

    public function triggerStrategyScan(Request $request, Response $response, Array $args)
    {
        $uid = $args['uid'];
        if(empty($uid)){
            $this->flash->addMessage('flash','No record specified');
            return $response->withRedirect($request->getUri()->getBaseUrl().$this->router->pathFor('homepage'));
        }
        $strategy = R::load('strategies', $uid);
        if($strategy->id==0){
            $this->flash->addMessage('flash','No record found');
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
        }
        $instrument = $strategy->instrument;
        $noCandles  = 200;
        $endTime    = time();
        $gran       = 'D';
        $candleBeans = R::find(
            'candle',
            ' instrument = :instrument AND gran = :gran AND candletime <= :endTime ORDER BY date DESC LIMIT :candles',
            [
                ':instrument' => $instrument,
                ':gran' => $gran,
                ':candles' => $noCandles,
                ':endTime'  => $endTime
            ]
        );
        $total = count($candleBeans);
        if($total>10){
            $newCandles = R::exportAll($candleBeans);
        } else {
            $this->flash->addMessage('flash', "Not enough data");
            return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
        }
        $args = array(
            'time'       => time(),
            'instrument' => $instrument,
            'gran'       => $gran,
            'strategyId' => $strategy->id,
            'signal'     => $strategy->signal,
            'params'     => $strategy->params,
        );
        $job = 'App\Job\Analyse';
        $counter = $total;
        foreach ($newCandles as $newCandle) {
            $args['analysisCandle'] = $newCandle['candletime'];
            $jobId = Resque::enqueue('medium', $job, $args, true);
            $this->logger->log(
                LogLevel::INFO,
                'Queuing Job {jobid} for {instrument}',
                array('jobid'    => $jobId,
                    'instrument' => $newCandle['instrument'],
                )
            );
            // leave 10 candles for the analysis
            $counter--;
            if($counter<10)
                continue;
        }
        $this->flash->addMessage('flash','Analysis Queued');
        return $response->withRedirect($request->getUri()->getBaseUrl() . $this->router->pathFor('adminstrategies'));
    }

    public function recommendations(Request $request, Response $response, Array $args)
    {
        $data = [];
        //TODO: get the most recent recommendations. this could show the results too.
        // Limit to 20.
        // Recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr, gran, expiry]

        $recommendations = R::findAll('recommendations', ' ORDER BY expiry DESC LIMIT 20');
        foreach ($recommendations as $recommendation) {
            $strategy = $recommendation->fetchAs('strategies')->strategy;
            $recommendation->signal = $strategy->signal;
        }

        $data['recommendations'] = $recommendations;
        $this->view->render($response, 'recommendations.twig', $data);
        return $response;
    }
}
