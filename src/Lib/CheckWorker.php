<?php
declare(strict_types=1);

namespace Monitor\Lib;


use Cake\Core\Configure;
use Cake\Core\InstanceConfigTrait;
use Cake\Utility\Hash;
use Monitor\Error\SentryHandler;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;

class CheckWorker
{
    use InstanceConfigTrait;

    /**
     * @var \React\EventLoop\LoopInterface
     */
    protected $_loop;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    protected $_defaultConfig = [
        'worker' => [
            'interval' => 60,
            'callback' => null,
            'onFailure' => null,
            'lastResult' => null,
            'success' => true
        ],
        'checks' => [],
        'failureHandler' => 'Sentry',
        'dump' => [
            'interval' => 1,
            'filePath' => TMP . 'monitor_dump.json'
        ]
    ];

    /**
     * constructor
     *
     * @param LoopInterface            $loop instance of react loop interface
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(LoopInterface $loop, LoggerInterface $logger)
    {
        $this->_loop = $loop;
        $this->_logger = $logger;

        $this->loadChecks();
    }

    /**
     * Loads the checks
     *
     * @return void
     */
    public function loadChecks(): void
    {
        $this->setConfig(Configure::read('ShellMonitor'));

        foreach ($this->getConfig('checks', []) as $checkName => $configuration) {
            $configuration = Hash::merge($this->getConfig('worker'), $configuration);

            $this->_loop->addPeriodicTimer($configuration['interval'], function () use ($checkName, $configuration) {
                $this->runCheck($checkName, $configuration);
            });
        }

        $this->_loop->addTimer($this->getConfig('dump.interval'), [$this, 'createDump']);
    }

    /**
     * Runs a single check
     *
     * @param string $name          Name of the check
     * @param array  $configuration Configuration of the check
     * @return bool
     * @throws \Exception
     */
    public function runCheck(string $name, array $configuration): bool
    {
        $result = null;
        if (is_callable($configuration['callback'])) {
            try {
                $result = $configuration['callback']();
            } catch (\Throwable $t) {
                $this->onError($name, $configuration, $t);
            }
        }

        $success = $result !== false;

        $this->setConfig('checks', [
            $name => [
                'lastResult' => $result,
                'success' => $success
            ]
        ]);

        if ($result === false) {
            $this->onFailure($name, $configuration);
            $this->_logger->alert("Failed: $name");
        } else {
            $this->_logger->info("Success: $name");
        }

        return $success;
    }

    /**
     * Creates a json dump.
     *
     * @return void
     */
    public function createDump(): void
    {
        $data = [];
        foreach ($this->getConfig('checks', []) as $checkName => $configuration) {
            $data[$checkName] = [
                'result' => $configuration['lastResult'],
                'success' => $configuration['success']
            ];
        }

        $filePath = $this->getConfig('dump.filePath');

        file_put_contents($filePath, json_encode($data));
    }

    /**
     * On failure
     *
     * @param string $name          Name of the check
     * @param array  $configuration Configuration of the check
     * @return void
     */
    public function onFailure(string $name, array $configuration): void
    {
        if (is_callable($configuration['onFailure'])) {
            $configuration['onFailure']($name, $configuration);
        }
    }

    /**
     * On error
     *
     * @param string     $name          Name of the check
     * @param array      $configuration Configuration of the check
     * @param \Throwable $throwable     Exception that occurred during check execution
     * @throws \Exception
     * @return void
     */
    public function onError(string $name, array $configuration, \Throwable $throwable): void
    {
        $failureHandler = $this->getConfig('failureHandler');

        if (!empty($configuration['failureHandler'])) {
            $failureHandler = $configuration['failureHandler'];
        }

        if ($failureHandler === 'Sentry') {
            $sentryHandler = new SentryHandler();
            $sentryHandler->handle($throwable);
        } else {
            throw new \Exception("Undefined failure handler $failureHandler in check $name");
        }
    }

    /**
     * Runs the worker
     *
     * @return void
     */
    public function run(): void
    {
        $this->_loop->run();
    }
}
