<?php
return [
    'CakeMonitor' => [
        'accessToken' => null,
        'projectName' => null,
        'serverDescription' => 'Server: ' . env('SERVERDESCRIPTION'),
        'onSuccess' => static function(): void {
            echo 'CHECK-OK';
        },
        'checks' => [],
        'Sentry' => [
            'enabled' => false,
            'dsn' => null,
            'sanitizeFields' => [],
            'sanitizeExtraCallback' => null,
            'extraDataCallback' => null
        ]
    ]
];
