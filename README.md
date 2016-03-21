# Monitor plugin for CakePHP 3.0

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

## License

The MIT License (MIT)

Copyright (c) 2016 scherer software

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.