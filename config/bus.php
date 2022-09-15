<?php

return [
    'scan_dirs' => [
        base_path('src/**/*'),
    ],
    'event'     => [
        'connection'  => env('EVENT_DRIVER', 'memory'),
        'connections' => [
            'memory' => [
                'driver' => 'memory',
            ],
        ],
    ],
    'query'     => [],
    'command'   => [],
];
