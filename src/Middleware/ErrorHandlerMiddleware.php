<?php
namespace Monitor\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware as CoreErrorHandlerMiddleware;
use Monitor\Error\SentryHandler;

class ErrorHandlerMiddleware extends CoreErrorHandlerMiddleware
{

    /**
     * {@inheritDoc}
     */
    public function handleException($exception, $request, $response)
    {
        $sentryHandler = new SentryHandler();
        $sentryHandler->handle($exception);
        return parent::handleException($exception, $request, $response);
    }
}
