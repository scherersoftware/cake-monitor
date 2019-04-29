<?php
declare(strict_types = 1);

namespace Monitor\Shell;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;
use Monitor\Lib\CheckWorker;
use React\EventLoop\Factory;

class MonitorShell extends Shell
{
    /**
     * main() method.
     *
     * @return void
     */
    public function main(): void
    {
        $loop = Factory::create();

        $logger = Log::engine('stdout');
        if (isset($this->params['logger']) && Log::engine($this->params['logger']) !== false) {
            $logger = Log::engine($this->params['logger']);
        }

        $worker = new CheckWorker($loop, $logger);
        $worker->run();
    }
}
