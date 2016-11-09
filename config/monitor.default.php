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
    ]
];
