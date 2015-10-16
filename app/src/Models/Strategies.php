<?php

namespace App\Models;


use Slim\Exception\Exception;

class Strategies extends \RedBeanPHP\SimpleModel
{
    /**
     * Called before SQL writer. encodes params/options from array/stdobject
     */
    public function update() {
        if(!is_string($this->params)){
            $this->params = json_encode($this->params);
        }
    }

    /**
     * Called after SQL writer. decode params
     */
    public function after_update(){
        if(is_string($this->params)){
            $this->params = (array) json_decode($this->params);
        }
    }

    /**
     * Called after bean loader. decode params
     */
    public function open(){
        if(is_string($this->params)){
            $this->params = (array) json_decode($this->params);
        }
    }

    /**
     * Checks if strategy is configured and okay to be subscribed to
     * @return bool
     */
    public function is_subscribable(){
        $bool = FALSE;
        $init = 0;
        $candles = array(1,2,3,4,5,6,7,8,9,10);
        $args = $this->params;
        //full namespace to signal
        $class = $this->signal;
        $signalClass = 'App\\Signals\\'.$class;
        try{
            $init = new $signalClass($args,$candles);
        } catch (\Exception $e){
            $init = 0;
        }
        if (!empty($init)) {
            $bool=TRUE;
        }
        return $bool;
    }
}