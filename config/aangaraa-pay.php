<?php

return [
    'mtn' => [
        'app_key' => env('MTN_APP_KEY'),
        'callback_url' => env('MTN_CALLBACK_URL'),
        'notify_url' => env('MTN_NOTIFY_URL'),
    ],

    'orange' => [
        'app_key' => env('ORANGE_APP_KEY'),
        'callback_url' => env('ORANGE_CALLBACK_URL'),
        'notify_url' => env('ORANGE_NOTIFY_URL'),
    ],
];
