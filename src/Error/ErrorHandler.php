<?php
declare(strict_types = 1);
namespace Monitor\Error;

use Cake\Error\ErrorHandler as CoreErrorHandler;
use ErrorException;
use Throwable;
use function Sentry\captureException;

class ErrorHandler extends CoreErrorHandler
{
    /**
     * {@inheritDoc}
     */
    public function handleError(
        int $code,
        string $description,
        ?string $file = null,
        ?int $line = null,
        ?array $context = null
    ): bool {
        $exception = new ErrorException($description, $code, 1, $file, $line);
        captureException($exception);

        return parent::handleError($code, $description, $file, $line, $context);
    }

    /**
     * {@inheritDoc}
     */
    public function handleException(Throwable $exception): void
    {
        captureException($exception);

        parent::handleException($exception);
    }
}
