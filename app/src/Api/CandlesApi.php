<?php

namespace App\Api;


use App\Action\Controller;
use GuzzleHttp\Psr7\LazyOpenStream;
use RedBeanPHP\R;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;

class CandlesApi extends Controller
{
    /**
     * @param Request $request | $args
     * @param Response $response
     * @param array $args
     * @return Response containing candle data as a CSV file
     */
    function dispatch(Request $request, Response $response, Array $args)
    {
        $id = $this->authenticator->getIdentity();
        if (empty($id)) {
            //TODO: return json instead
            $path = __DIR__;
            $newStream = new LazyOpenStream($path . '/../../../public/data.csv', 'r');
        } else {
            // get the current instrument and send some data
            //Defaults
            require_once __DIR__ . '/../../loadsettings.php';
            $settings = loadsettings();
            $pairs = $settings['oanda']['pairs'];
            $grans = ['H1', 'D'];
            $days = 200;
            $fp = fopen('php://temp', 'r+b');
            $newStream = new Stream($fp);
            $newStream->write('Date,Open,High,Low,Close,Volume' . PHP_EOL);
            $instrument = $request->getParam('instrument');
            if (!in_array($instrument, $pairs)) {
                $instrument = $pairs[1];
            }
            $gran = $request->getParam('gran'); // D or H1
            if (!in_array($gran, $grans)) {
                $gran = 'D';
            }
            $candles = R::find(
                'candle',
                ' instrument = :instrument AND gran = :gran ORDER BY date DESC LIMIT :days',
                [
                    ':instrument' => $instrument,
                    ':gran' => $gran,
                    ':days' => $days
                ]
            );
            if (!empty($candles)) {
                foreach ($candles as $candle) {
                    //TODO time needs to be 17-aug-2015,
                    $data = [];
                    $candletime = new \DateTime('@' . $candle->candletime);
                    if ($gran == 'D') {
                        $data['Date'] = $candletime->format('d-M-y');
                    } else {
                        $data['Date'] = $candletime->format('Y-m-d H:i:s');
                    }
                    $data['Open'] = $candle->open;
                    $data['High'] = $candle->high;
                    $data['Low'] = $candle->low;
                    $data['Close'] = $candle->close;
                    $data['Volume'] = '0';
                    fputcsv($fp, $data, $delimiter = ',', $enclosure = '"');

                }
            }


            $newStream->rewind();

        }
        $newResponse = $response->withBody($newStream);
        return $newResponse;
    }
}