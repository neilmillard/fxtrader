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
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr]
                    'trade'=>false,
                    'instrument'=>'',
                    'side'=>'',
                    'entry'=>'',
                    'stopLoss'=>'',
                    'rr'=>1
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
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr]
                    'trade'=>true,
                    'instrument'=>'EUR_USD',
                    'side'=>'buy',
                    'entry'=>'1.4110',
                    'stopLoss'=>'1.4055',
                    'rr'=>1
                )
            ),
            array(
                'bear flag',
                array( //candles
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4000,'high'=>1.4005,'low'=>1.3950,'close'=>1.3970,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3970,'high'=>1.4005,'low'=>1.3920,'close'=>1.3920,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3920,'high'=>1.4005,'low'=>1.3950,'close'=>1.3963,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3963,'high'=>1.4005,'low'=>1.3950,'close'=>1.3990,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3990,'high'=>1.4005,'low'=>1.3950,'close'=>1.4030,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4030,'high'=>1.4040,'low'=>1.3950,'close'=>1.4090,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.4090,'high'=>1.4091,'low'=>1.3950,'close'=>1.3995,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3995,'high'=>1.4000,'low'=>1.3910,'close'=>1.3910,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3910,'high'=>1.3915,'low'=>1.3828,'close'=>1.3830,'complete'=>true,'gran'=>'D'),
                    array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>1.3830,'high'=>1.3855,'low'=>1.3828,'close'=>1.3852,'complete'=>true,'gran'=>'D'),
                ),
                array( //args
                    'noOfPoleCandles'=>2,
                    'maxBreatherCandles'=>1,
                    'percentBreatherSize'=>0.3,
                    'strongPoleCandleCent'=>0.9,
                    'entryBufferPips'=>0.0005,
                    'instrument'=>'EUR_USD'
                ),
                array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr]
                    'trade'=>true,
                    'instrument'=>'EUR_USD',
                    'side'=>'sell',
                    'entry'=>'1.3823',
                    'stopLoss'=>'1.3860',
                    'rr'=>1
                )
            )
        );
    }
}
