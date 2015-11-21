<?php
return [
    'GetDayCandles' => [
        'cron' => "5 22 * * *",
        'class' => '\App\Job\OandaSystem\GetDayCandles',
        'args' => [
            'queue' => 'low',
            'time' => time(),
            'candles' => '2',
        ],
        'description' => "This daily schedule will run just after 22:00 and fetch the daily candle info",
    ],
    'GetHourCandles' => [
        'cron' => "1 * * * *",
        'class' => '\App\Job\OandaSystem\GetHourCandles',
        'args' => [
            'queue' => 'low',
            'time' => time(),
            'candles' => '2',
        ],
        'description' => "This daily schedule will run just after every hour and fetch the hourly candle info",
    ]
];
