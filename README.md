# Monitor plugin for CakePHP 3.0

A simple config based monitoring plugin for CakePHP 3

## Installation

Load the plugin in your `config/bootstrap.php`

    Plugin::load('Monitor', ['bootstrap' => true, 'routes' => true]);

Add configuration to your `config/app.php`

    'CakeMonitor' => [
        'accessToken' => 'Header token (CAKEMONITORTOKEN) used for authentication',
        'projectName' => 'Name of the Cake Project',
        'serverDescription' => 'Identifier of the server - use of env() is recommended',
        'onSuccess' => function() {
            die('Do things if everything is fine');
        },
        'checks' => [
            'Name of Check' => [
                'callback' => function() {
                    if ($error == true) {
                        return 'Specific error message';
                    }
                    return true;
                },
                'error' => 'General error message for this check'
            ]
        ]
    ]