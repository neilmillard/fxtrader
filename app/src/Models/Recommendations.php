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
    /**
     * @param array $recommendAr
     * @return \RedBeanPHP\OODBBean
     */
    public static function factory($recommendAr){
        $theBean = R::findOrCreate('recommendations',
            [ 'expiry' => $recommendAr['expiry'],
                'instrument' => $recommendAr['instrument'] ]);
        // don't update if recommendation already exists
        if(empty($theBean->id)){
            $theBean->import(
                $recommendAr,
                [   'instrument',
                    'side',
                    'entry',
                    'stopLoss',
                    'rr' ,
                    'gran',
                    'expiry'
                ]
            );
        }
        return $theBean;
    }
}