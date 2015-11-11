<?php

namespace App\Models;
use RedBeanPHP\R;
use \RedBeanPHP\SimpleModel;

/*
 * array( //recommendation: [trade, instrument, side, entry, stopLoss, stopLossPips, rr, gran, expiry]
            'trade'=>false,
            'instrument'=>'',
            'side'=>'',
            'entry'=>'',
            'stopLoss'=>'',
            'rr' => 1,
            'gran' => 'D',
            'expiry' => 0
        )
 * also has strategy one to many relation
 */
class Recommendations extends SimpleModel
{
    public function factory(Array $recommendAr){
        $theModel = R::findOrCreate('recommendations',
            [ 'expiry' => $recommendAr['expiry'],
                'instrument' => $recommendAr['instrument'] ]);
        if(empty($theModel->id)){
            $theModel->import($recommendAr);
        }
        return $theModel;
    }

    public function setStrategy($strategyId){
        $strategy = R::load('strategies',$strategyId);
        $this->strategy = $strategy;
        R::store($this->bean);
    }
}