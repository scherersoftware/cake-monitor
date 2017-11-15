<?php
return [
    'CakeMonitor' => [
        'accessToken' => null,
        'projectName' => null,
        'serverDescription' => 'Server: ' . env('SERVERDESCRIPTION'),
        'onSuccess' => function() {
            echo 'CHECK-OK';
            return;
        },
        'checks' => [],
        'Sentry' => [
            'enabled' => false,
            'dsn' => null,
            'sanitizeFields' => [],
            'sanitizeExtraCallback' => null
        ]
    ],
    'ShellMonitor' => [
        'checks' => [
            'foo' => [
                'interval' => 1,
                'callback' => function() {
                    return false;
                }
            ]
        ],
        'dump' => [
            'interval' => 1,
            'filePath' => TMP . 'monitor_dump.json'
        ]
    ]
];
