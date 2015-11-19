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
        $queueNames = \Resque::queues();
        foreach ($queueNames as $queue) {
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
                'size' => $size,
                'jobs' => $this->peek($queue)
            ];
        }

        $data['queues'] = $queues;
        $this->view->render($response, 'queues.twig', $data);
        return $response;
    }

    /**
     * Peek
     *
     * @param string $queue The name of the queue
     * @param integer $start
     * @param integer $count
     *
     * @return array List of jobs
     *
     */
    public static function peek($queue, $start = 0, $count = 1000)
    {
        $jobs = \Resque::redis()->lrange('queue:' . $queue, $start, $count);
        $curr_jobs = array();
        if (is_array($jobs)) {
            foreach ($jobs as $job) {
                $curr_jobs[] = json_decode($job, TRUE);
            }
        }

        return $curr_jobs;
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
                'job' => print_r($job['payload'],true),
            ];
        }

        $data['workers'] = $workers;
        $this->view->render($response, 'workers.twig', $data);
        return $response;
    }
}