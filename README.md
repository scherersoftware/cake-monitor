# CakePHP 3 cake-monitor

[![License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)

A simple config based monitoring plugin for CakePHP 3

## Installation

#### 1. require the plugin in your `composer.json`

		"require": {
			...
			"scherersoftware/cake-monitor": "dev-master",
			...
		}

#### 2. Include the plugin using composer
Open a terminal in your project-folder and run these commands:

	$ composer update
	$ composer install


#### 3. Load the plugin in your `config/bootstrap.php`

	Plugin::load('Monitor', ['bootstrap' => true, 'routes' => true]);

#### 4. Add configuration to your `config/app.php`

	    'CakeMonitor' => [
	        'accessToken' => 'Header token (CAKEMONITORTOKEN) used for authentication',
	        'projectName' => 'Name of the Cake Project',
	        'serverDescription' => 'Identifier of the server - use of env() is recommended',
	        'onSuccess' => function() {
	        	// callback function in case every check was successful
	            die('Do things if everything is fine');
	        }
	    ]

Note that the Header token (`accessToken`) is needed to grant access to the monitoring URL. Treat this token confidentially if your checking functions reveal classified information about your project.
Use a suitable browser-plugin to modifiy your HTTP request header when you're calling the monitoring-URL.

## Usage

By default this plugin triggers a status check on all MySQL tables of the project.
This behavior can be overwritten in app.php.


### Define custom check-functions

Define custom check functions in your `app.php`. Checks can be defined as array fields with anonymous callback-functions here. The Array `'checks'` is merged with the one in  `vendor/scherersoftware/cake-monitor/config/monitor.default.php` which contains the default database checking function.

You can use that function as reference to implement any checking function you want.

	'CakeMonitor' => [
	    'accessToken' => 'CAKEMONITORTOKEN',
	    'projectName' => 'Name of the Cake Project',
	    'serverDescription' => 'Identifier of the server - use of env() is recommended',
	    'onSuccess' => function() {
	        // callback function in case every check was successful
	        die('Do things if everything is fine');
	    },
	    'checks' => [
		    'FUNCTION_NAME' => [
		        'callback' => function() {
		            // your check function
		            // see the default 'DATABASE' function for further information
		            return true;
		        }
		    ]
		]
	]


If every checking function executes without any exceptions, the `'onSuccess'` callback function is called.


## Call

Run the current checks and see their output anytime by calling the following URL: `http://YOUR_PROJECT_URL.tld/monitor`

## Sentry Error Reporting

The plugin contains functionality to hook into CakePHP's error reporting and send exceptions to the excellent error reporting service [Sentry](https://getsentry.com/).

### Configuration

The `CakeMonitor` configuration section in your `app.php` must contain a `Sentry` key

    'Sentry' => [
        'enabled' => !Configure::read('debug'), # Boolean value to enable sentry error reporting
        'dsn' => '', # The DSN for the Sentry project. You find this on the Sentry Project Settings Page.
        'sanitizeFields' => [ # An array of fields, whose values will be removed before sending
                              # data to sentry. Be sure to include fields like session cookie names, 
                              # sensitive environment variables and other private configuration.
            'password',
            'rememberuser',
            'auth_token',
            'api_token',
            'mysql_password',
            'email_password',
            'cookie'
        ],
        // Optional callback for special filtering
        'sanitizeExtraCallback' => function (&$data) {
            if (isset($data['user']['id'])) {
                $data['user']['id'] = '*****';
            }
        },
    ]

In your `bootstrap.php` you have to tell CakePHP which ErrorHandler to use. Please find the following section:

    /**
     * Register application error and exception handlers.
     */
    $isCli = PHP_SAPI === 'cli';
    if ($isCli) {
        (new ConsoleErrorHandler(Configure::read('Error')))->register();
    } else {
        (new ErrorHandler(Configure::read('Error')))->register();
    }

And modify it to look like this:

    /**
     * Register application error and exception handlers.
     */
    Plugin::load('Monitor', ['bootstrap' => true, 'routes' => true]); # important for loading and merging the configuration

    $isCli = php_sapi_name() === 'cli';
    if ($isCli) {
        (new \Monitor\Error\ConsoleErrorHandler(Configure::consume('Error')))->register();
    } else {
        (new \Monitor\Error\ErrorHandler(Configure::consume('Error')))->register();
    }    

From now on, given that the configuration value `CakeMonitor.Sentry.enabled` is true, Errors and Exceptions are reported to Sentry without changing any of CakePHP's default ErrorHandler behavior.

### Examples
Loggin an exception into sentry:

    $sentryHandler = new SentryHandler();
    $sentryHandler->handle($exception);

Logging a message into sentry:

    $sentryHandler = new SentryHandler();
    $sentryHandler->captureMessage('Error within request.', null, [
        'extra' => [
            'result' => $result,
            'status' => $status
        ]
    ]);