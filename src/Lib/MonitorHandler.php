<?php
declare(strict_types = 1);
namespace Monitor\Lib;

use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Exception;

/**
 * Used for processing monitor checks
 */
class MonitorHandler
{
    /**
     * Configuration that is used by the methods of this class
     *
     * @var array
     */
    protected $_config = [];

    /**
     * Reference of the current request object
     *
     * @var \Cake\Http\ServerRequest
     */
    public $request;

    /**
     * Reference of the current response object
     *
     * @var \Cake\Http\Response
     */
    public $response;

    /**
     * Constructor
     *
     * @param \Cake\Http\ServerRequest $request Current Request
     * @param \Cake\Http\Response $response Current Response
     */
    public function __construct(ServerRequest $request, Response $response)
    {
        $this->_config = Configure::read('CakeMonitor');
        $this->_validateConfig();

        $this->request = $request;
        $this->response = $response;
    }

    /**
     * Validates Config
     *
     * @throws \Exception if configuration is incomplete
     * @return void
     */
    protected function _validateConfig(): void
    {
        foreach ($this->_config as $key => $value) {
            if (!isset($value)) {
                throw new Exception('Incomplete configuration: ' . $key, 1);
            }
        }
    }

    /**
     * Handle authentication by header token
     *
     * @return void
     */
    public function handleAuth(): void
    {
        if ($this->request->getHeader('CAKEMONITORTOKEN') !== $this->_config['accessToken']) {
            die('NOT AUTHENTICATED');
        }
    }

    /**
     * Handle all defined checks
     *
     * @return \Cake\Http\Response
     */
    public function handleChecks(): Response
    {
        $errors = [];
        foreach ($this->_config['checks'] as $name => $check) {
            if (empty($check)) {
                continue;
            }
            $result = $check['callback']();
            if ($result !== true) {
                $errors[] = $name . ': <br>' . $check['error'] . ' - ' . $result;
            }
        }
        if (!empty($errors)) {
            $this->response = $this->response->withStatus(500);

            echo date('Y-m-d H:i:s') . ': ' . $this->_config['projectName'] . ' - ' . $this->_config['serverDescription'] . ' - Status Code: ' . $this->response->getStatusCode() . '<br><br> ';
            foreach ($errors as $error) {
                echo $error . '<br><br>';
            }

            return $this->response;
        }
        $this->_config['onSuccess']();

        return $this->response;
    }
}
