<?php
declare(strict_types = 1);
namespace Monitor\Middleware;

use Cake\Error\Middleware\ErrorHandlerMiddleware as CoreErrorHandlerMiddleware;
use Monitor\Error\SentryHandler;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class ErrorHandlerMiddleware extends CoreErrorHandlerMiddleware
{
    /**
     * {@inheritDoc}
     */
    public function handleException(Throwable $exception, ServerRequestInterface $request): ResponseInterface
    {
        $sentryHandler = new SentryHandler();
        $sentryHandler->handle($exception);

        return parent::handleException($exception, $request);
    }
}
