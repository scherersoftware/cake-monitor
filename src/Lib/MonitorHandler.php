<?php
namespace Monitor\Lib;

use Cake\Core\Configure;
use Cake\Network\Response;
use Cake\Network\Request;
use Cake\Utility\Hash;

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
     * Instance of the current request object
     * 
     * @var Cake\Network\Http\Request
     */
    protected $_request;

    /**
     * Instance of the current response object
     * 
     * @var Cake\Network\Http\Response
     */
    protected $_response;

    /**
     * Constructor
     * 
     * @param Request $request Current Request
     * @param Response $response Current Response
     * @return void
     */
    public function __construct(Request $request, Response $response)
    {
        $this->_config = Configure::read('CakeMonitor');

        $this->_validateConfig();

        $this->_request = $request;
        $this->_response = $response;
    }

    /**
     * Validates Config
     * 
     * @throws \Exception if configuration is incomplete
     * @return void
     */
    protected function _validateConfig()
    {
        foreach ($this->_config as $key => $value) {
            if (empty($value)) {
                throw new \Exception('Incomplete configuration: ' . $key, 1);
            }
        }
    }

    /**
     * Handle authentication by header token
     *
     * @return void
     */
    public function handleAuth()
    {
        if ($this->_request->header('CAKEMONITORTOKEN') !==  $this->_config['accessToken']) {
            die('NOT AUTHENTICATED');
        }
    }


    /**
     * Handle all defined checks
     *
     * @return void
     */
    public function handleChecks()
    {
        $errors = [];
        foreach ($this->_config['checks'] as $name => $check) {
            $result = $check['callback']();
            if ($result !== true) {
               $errors[] = $name . ': <br>' . $check['error'] . ' - ' . $result;
            }
        }
        if (!empty($errors)) {
            echo date('Y-m-d H:i:s') . ': ' . $this->_config['projectName'] . ' - ' . $this->_config['serverDescription'] . ' - Status Code: ' . $this->_response->statusCode() . '<br><br> ';
            foreach ($errors as $error) {
                echo $error . '<br><br>';
            }
            die();
        }
        $this->_config['onSuccess']();
        die();
    }
}