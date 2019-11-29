<?php
declare(strict_types = 1);
namespace Monitor\Controller;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Monitor\Lib\MonitorHandler;

class AppController extends Controller
{
    /**
     * Instance of the Monitor Lib
     *
     * @var \Monitor\Lib\MonitorHandler
     */
    protected $_monitor;

    /**
     * {@inheritDoc}
     */
    public function beforeFilter(EventInterface $event): Response
    {
        $this->_monitor = new MonitorHandler($this->getRequest(), $this->getResponse());

        $this->_monitor->handleAuth();

        return $this->_monitor->handleChecks();
    }
}
