<?php

namespace App\Api;


use App\Action\Controller;
use Slim\Http\Request;
use Slim\Http\Response;

class CandlesApi extends Controller
{
    function dispatch(Request $request, Response $response, Array $args)
    {
        $id = $this->authenticator->getIdentity();
        if (empty($id)) {
            //TODO: return json instead
            $path = __DIR__;
            $newStream = new \GuzzleHttp\Psr7\LazyOpenStream($path . '/../../../public/data.csv', 'r');
            $newResponse = $response->withBody($newStream);
            return $newResponse;
        } else {
            // get the current instrument and send some data
            //TODO time needs to be 17-aug-2015,
            $data = json_encode([
                'Date' => time(),
                'Open' => 0.0034,
                'High' => 0.2039,
                'Low' => 0.2039,
                'Close' => 0.2309,
                'Volume' => 299344,
            ]);
        }

        $response->withJson($data);
        return $response;
    }
}