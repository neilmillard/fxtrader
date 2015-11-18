<?php
return [
    'schedule_1' => [
        'cron' => "5 22 * * *",
        'class' => '\App\Job\OandaSystem\GetDayCandles',
        'args' => [
            'queue' => 'low',
            'time' => time(),
            'days'   => '2',
        ],
        'description' => "long description",
    ]
];
