<?php
namespace Monitor\Lib;

class SanitizeCallbackDataProcessor extends \Raven_Processor
{
    protected $_callback = null;

    /**
     * Override the default processor options
     *
     * @param array $options    Associative array of processor options
     */
    public function setProcessorOptions(array $options)
    {
        if (isset($options['callback'])) {
            $this->_callback = $options['callback'];
        }
    }

    /**
     * Processor
     *
     * @param array &$data Data
     * @return void
     */
    public function process(&$data)
    {
        if (is_callable($this->_callback)) {
            $this->_callback->__invoke($data);
        }
    }
}
