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
                'Not enough candles',
                array( //candles
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
                        array('date'=>'2015','instrument'=>'EUR_USD','candletime'=>'102923494','open'=>0.0000,'high'=>0.0000,'low'=>0.0000,'close'=>0.0000,'complete'=>true,'gran'=>'D'),
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
            )
        );
    }
}
