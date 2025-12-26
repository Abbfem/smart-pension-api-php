<?php

declare(strict_types=1);

namespace NestPension\Exceptions;

/**
 * Exception for invalid environment configuration.
 */
class InvalidEnvironmentException extends NestException
{
    public function __construct(string $message = 'Invalid environment configuration', int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
