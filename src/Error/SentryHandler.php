<?php
namespace Monitor\Error;

use Cake\Core\Configure;
use Exception;

class SentryHandler
{
    /* @var \Raven_Client $_ravenClient  */
    protected $_ravenClient = nulL;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_ravenClient = new \Raven_Client(Configure::read('CakeMonitor.Sentry.dsn'), [
            'processorOptions' => [
                'Raven_SanitizeDataProcessor' => [
                    'fields_re' => '/(' . implode('|', Configure::read('CakeMonitor.Sentry.sanitizeFields')) . ')/i'
                ]
            ]
        ]);
    }

    /**
     * Exception Handler
     *
     * @param Exception $exception Exception to handle
     * @return void
     */
    public function handle(Exception $exception)
    {
        if (!Configure::read('CakeMonitor.Sentry.enabled') || error_reporting() === 0) {
            return false;
        }

        $errorHandler = new \Raven_ErrorHandler($this->_ravenClient);
        $errorHandler->registerShutdownFunction();
        $errorHandler->handleException($exception);
    }

    /**
     * Capture a message via sentry
     *
     * @param string $message Message to be captured
     * @param array $params Additional parameters
     * @param array $levelOrOptions Level or options
     * @param bool $stack Print stack trace
     * @param null $vars Variables
     * @return bool
     */
    public function captureMessage($message, $params=array(), $levelOrOptions=array(), $stack=false, $vars = null)
    {
        if (!Configure::read('CakeMonitor.Sentry.enabled') || error_reporting() === 0) {
            return false;
        }

        return $this->_ravenClient->captureMessage($message, $params, $levelOrOptions, $stack, $vars);
    }
}
