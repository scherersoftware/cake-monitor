<?php
namespace Monitor\Error;

use Cake\Core\Configure;
use Exception;

class SentryHandler
{

    /**
     * Constructor
     *
     */
    public function __construct()
    {
    }

    /**
     * Exception Handler
     *
     * @param Exception $exception Exception to handle
     * @return void
     */
    public function handle(Exception $exception)
    {
        if (!Configure::read('CakeMonitor.Sentry.enabled')) {
            return;
        }

        $client = new \Raven_Client(Configure::read('CakeMonitor.Sentry.dsn'), [
            'processorOptions' => [
                'Raven_SanitizeDataProcessor' => [
                    'fields_re' => '/(' . implode('|', Configure::read('CakeMonitor.Sentry.sanitizeFields')) . ')/i'
                ]
            ]
        ]);
        $errorHandler = new \Raven_ErrorHandler($client);
        $errorHandler->registerShutdownFunction();
        $errorHandler->handleException($exception);
    }
}
