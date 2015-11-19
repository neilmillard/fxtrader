<?php

namespace App\test;


use App\Signals\Flag;

class FlagTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){
        parent::setUp();
    }

    public function tearDown(){
        parent::tearDown();
    }

    /**
     * @dataProvider constructProvider
     * @param $args
     * @param $candles
     * @param $expected
     */
    public function testIsRightObject($args,$candles,$expected){
        $myFlag = new Flag($args,$candles);
        $this->assertEquals('The Flag',$myFlag->name,'ConstructFailed');
    }
    /**
     * @dataProvider constructProvider
     * @param $args
     * @param $candles
     * @param $expected
     */
    public function testShowArgsArray($args,$candles,$expected){
        $myFlag = new Flag($args,$candles);
        $this->assertNotEmpty($myFlag->showArgs(),'showArgs needs to return an Array that is not empty');
    }

    public function constructProvider(){
        return array(
            array(
                array('noOfPoleCandles'=>2,
                    'maxBreatherCandles'=>1,
                    'percentBreatherSize'=>0.3,
                    'strongPoleCandleCent'=>0.9,
                    'entryBufferPips'=>0.0005,
                    'instrument'=>'EUR_USD'
                ),
                array(1,2,3,4,5,6,7,8,9,10),
                6 )
        );
    }

    /**
     * @depends testIsRightObject
     * @param $test
     * @param $candles
     * @param $args
     * @param $recommendation
     * @dataProvider analyseProvider
     */
    public function testAnalyse($test, $candles,$args,$recommendation){
        $myFlag = new Flag($args,$candles);
        $result = $myFlag->analyse();
        $strRecommendation = print_r($recommendation,true);
        $strResult = print_r($result,true);
        $this->assertEquals($result,$recommendation,$test.' '.$strResult.'!='.$strRecommendation);
    }

    public function analyseProvider(){
        return array(
            array(
                'random',
                array( //candles
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4000,'high'=>1.4005,'low'=>1.3950,'close'=>1.3970,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3970,'high'=>1.4005,'low'=>1.3920,'close'=>1.3920,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3920,'high'=>1.4005,'low'=>1.3950,'close'=>1.3963,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3963,'high'=>1.4005,'low'=>1.3950,'close'=>1.3990,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3990,'high'=>1.4005,'low'=>1.3950,'close'=>1.4030,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4030,'high'=>1.4040,'low'=>1.3950,'close'=>1.4090,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4090,'high'=>1.4091,'low'=>1.3950,'close'=>1.3995,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3995,'high'=>1.4005,'low'=>1.3920,'close'=>1.3930,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3930,'high'=>1.4005,'low'=>1.3885,'close'=>1.3890,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3890,'high'=>1.4005,'low'=>1.3850,'close'=>1.3850,'complete'=>true,'gran'=>'D'),
                ),
                array( //args
                    'noOfPoleCandles'=>2,
                    'maxBreatherCandles'=>1,
                    'percentBreatherSize'=>0.3,
                    'strongPoleCandleCent'=>0.9,
                    'entryBufferPips'=>0.0005,
                    'instrument'=>'EUR_USD'
                ),
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr, gran, expiry]
                    'trade'=>false,
                    'instrument'=>'',
                    'side'=>'',
                    'entry'=>'',
                    'stopLoss'=>'',
                    'rr' => 1,
                    'gran' => 'D',
                    'expiry' => 0
                )
            ),
            array(
                'bull flag',
                array( //candles
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4000,'high'=>1.4005,'low'=>1.3950,'close'=>1.3970,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3970,'high'=>1.4005,'low'=>1.3920,'close'=>1.3920,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3920,'high'=>1.4005,'low'=>1.3950,'close'=>1.3963,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3963,'high'=>1.4005,'low'=>1.3950,'close'=>1.3990,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3990,'high'=>1.4005,'low'=>1.3950,'close'=>1.4030,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4030,'high'=>1.4040,'low'=>1.3950,'close'=>1.4090,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4090,'high'=>1.4091,'low'=>1.3925,'close'=>1.3925,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3925,'high'=>1.4005,'low'=>1.3920,'close'=>1.4000,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4000,'high'=>1.4105,'low'=>1.3995,'close'=>1.4100,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4100,'high'=>1.4100,'low'=>1.4060,'close'=>1.4069,'complete'=>true,'gran'=>'D'),
                ),
                array( //args
                    'noOfPoleCandles'=>2,
                    'maxBreatherCandles'=>1,
                    'percentBreatherSize'=>0.3,
                    'strongPoleCandleCent'=>0.9,
                    'entryBufferPips'=>0.0005,
                    'instrument'=>'EUR_USD'
                ),
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr, gran, expiry]
                    'trade'=>true,
                    'instrument'=>'EUR_USD',
                    'side'=>'buy',
                    'entry'=>'1.4110',
                    'stopLoss'=>'1.4055',
                    'rr' => 1,
                    'gran' => 'D',
                    'expiry' => 102923494 + (60 * 60 * 48)
                )
            ),
            array(
                'bear flag',
                array( //candles
                    array('id' => '1018','date' => '2015-10-26','instrument' => 'EUR_USD','candletime' => '1445806800','open' => '1.1001','high' => '1.1068','low' => '1.0998','close' => '1.1059','complete' => '1','gran' => 'D'),
                    array('id' => '1017','date' => '2015-10-23','instrument' => 'EUR_USD','candletime' => '1445547600','open' => '1.1106','high' => '1.114' ,'low' => '1.0997','close' => '1.1017','complete' => '1','gran' => 'D'),
                    array('id' => '1016','date' => '2015-10-22','instrument' => 'EUR_USD','candletime' => '1445461200','open' => '1.1339','high' => '1.1351','low' => '1.11'  ,'close' => '1.1109','complete' => '1','gran' => 'D'),
                    array('id' => '1015','date' => '2015-10-21','instrument' => 'EUR_USD','candletime' => '1445374800','open' => '1.1345','high' => '1.1378','low' => '1.1334','close' => '1.1339','complete' => '1','gran' => 'D'),
                    array('id' => '1014','date' => '2015-10-20','instrument' => 'EUR_USD','candletime' => '1445288400','open' => '1.133' ,'high' => '1.1387','low' => '1.1324','close' => '1.1346','complete' => '1','gran' => 'D'),
                    array('id' => '1013','date' => '2015-10-19','instrument' => 'EUR_USD','candletime' => '1445202000','open' => '1.1361','high' => '1.1379','low' => '1.1306','close' => '1.1327','complete' => '1','gran' => 'D'),
                    array('id' => '1012','date' => '2015-10-16','instrument' => 'EUR_USD','candletime' => '1444942800','open' => '1.1394','high' => '1.1396','low' => '1.1334','close' => '1.1349','complete' => '1','gran' => 'D'),
                    array('id' => '1011','date' => '2015-10-15','instrument' => 'EUR_USD','candletime' => '1444856400','open' => '1.1473','high' => '1.1495','low' => '1.1363','close' => '1.1384','complete' => '1','gran' => 'D'),
                    array('id' => '1010','date' => '2015-10-14','instrument' => 'EUR_USD','candletime' => '1444770000','open' => '1.138' ,'high' => '1.1489','low' => '1.1378','close' => '1.1474','complete' => '1','gran' => 'D'),
                    array('id' => '1009','date' => '2015-10-13','instrument' => 'EUR_USD','candletime' => '1444683600','open' => '1.136' ,'high' => '1.1411','low' => '1.1344','close' => '1.1379','complete' => '1','gran' => 'D'),
                ),
                array( //args
                    'noOfPoleCandles'=>2,
                    'maxBreatherCandles'=>1,
                    'percentBreatherSize'=>0.3,
                    'strongPoleCandleCent'=>0.9,
                    'entryBufferPips'=>0.0005,
                    'instrument'=>'EUR_USD'
                ),
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr, gran, expiry]
                    'trade'=>true,
                    'instrument'=>'EUR_USD',
                    'side'=>'sell',
                    'entry'=>'1.0992',
                    'stopLoss'=>'1.1073',
                    'rr' => 1,
                    'gran' => 'D',
                    'expiry' => 1445806800 + (60 * 60 * 48)
                )
            )
        );
    }
}
