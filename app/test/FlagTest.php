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

    public function testIsRightObject(){
        $ourFlag = new Flag();
        $this->assertEquals('The Flag',$ourFlag->name);
    }

    public function testShowArgsArray(){
        $ourFlag = new Flag();
        $this->assertNotEmpty($ourFlag->showArgs(),'showArgs needs to return an Array that is not empty');
    }


}
