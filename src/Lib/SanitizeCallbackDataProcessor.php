<?php
declare(strict_types = 1);
namespace Monitor\Lib;

use Raven_Processor;

class SanitizeCallbackDataProcessor extends Raven_Processor
{
    /**
     * @var callable|null
     */
    protected $_callback;

    /**
     * Override the default processor options
     *
     * @param array $options    Associative array of processor options
     * @return void
     */
    public function setProcessorOptions(array $options): void
    {
        if (isset($options['callback'])) {
            $this->_callback = $options['callback'];
        }
    }

    /**
     * Processor
     *
     * @param array|mixed $data Data
     * @return void
     */
    public function process(&$data): void
    {
        if (is_callable($this->_callback)) {
            $function = $this->_callback;
            $function($data);
        }
    }
}
