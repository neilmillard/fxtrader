<?php
return [
    'schedule_1' => [
        'cron' => "5 10 * * *",
        'class' => '\App\Job\OandaSystem\GetDayCandles',
        'args' => [
            'queue' => 'low',
            'time' => time(),
            'userid' => 'not needed',
            'oanda' => [
                'apiKey' => '',
                'accountId' => '',
                'serverType' => 'Demo',
            ]
        ],
        'description' => "long description",
    ]
];