<?php

namespace App\Action;

use RedBeanPHP\R;
use Slim\Http\Request;
use Slim\Http\Response;

final class QueuesAction extends Controller
{
    public function admin(Request $request, Response $response, Array $args)
    {
        $this->logger->info("Admin Queuesaction page action dispatched");
        $data = [];
        // get workers
        $settings = loadsettings();
        $REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
        if (!empty($REDIS_BACKEND)) {
            \Resque::setBackend($REDIS_BACKEND);
        }
        $workers = \Resque::redis()->smembers('workers');
        $noworkers = count($workers);

        $data['workers'] = $workers;
        $data['noworkers'] = $noworkers;
        $data['nojobs'] = $this->getTotalJobs();
        $this->view->render($response, 'queuesindex.twig', $data);
        return $response;
    }

    /**
     * Returns the total number of jobs in the Redis Queues
     * @return int
     */
    private function getTotalJobs()
    {
        $totalJobs = 0;
        $queuenames = \Resque::queues();
        foreach ($queuenames as $queue) {
            $size = \Resque::size($queue);
            $totalJobs += $size;
        }
        return $totalJobs;
    }

    public function jobs(Request $request, Response $response, Array $args)
    {
        $settings = loadsettings();
        $REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
        if (!empty($REDIS_BACKEND)) {
            \Resque::setBackend($REDIS_BACKEND);
        }
        $queues = [];
        $queuenames = \Resque::queues();
        sort($queuenames);
        foreach ($queuenames as $queue) {
            $size = \Resque::size($queue);
            $queues[] = ['name' => $queue,
                'size' => $size
            ];
        }

        $data['queues'] = $queues;
        $this->view->render($response, 'queues.twig', $data);
        return $response;
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

    public function workers(Request $request, Response $response, Array $args)
    {
        $data = [];
        $settings = loadsettings();
        $REDIS_BACKEND = $settings['resque']['REDIS_BACKEND'];
        if (!empty($REDIS_BACKEND)) {
            \Resque::setBackend($REDIS_BACKEND);
        }
        $workerlist = \Resque_Worker::all();
        $workers = [];
        foreach ($workerlist as $worker) {
            $job = $worker->job();
            if (empty($job)) {
                $job = "Idle";
            }
            $workers[] = ['name' => (string)$worker,
                'job' => $job,
            ];
        }

        $data['workers'] = $workers;
        $this->view->render($response, 'workers.twig', $data);
        return $response;
    }
}