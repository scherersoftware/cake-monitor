<?php

namespace Monitor\Controller;

use Cake\Controller\Controller;
use Monitor\Lib\MonitorHandler;

class AppController extends Controller
{

    /**
     * Instance of the Monitor Lib
     * 
     * @var Cakemonitor\Lib\MonitorHandler
     */
    protected $_monitor;

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(\Cake\Event\Event $event)
    {
        $this->_monitor = new MonitorHandler($this->request, $this->response);

        $this->_monitor->handleAuth();

        $this->_monitor->handleChecks();
    }

    public function render($view = false, $layout = false)
    {
        return parent::render($view, $layout);
    }
}
