<?php

declare(strict_types=1);

namespace NestPension\Exceptions;

/**
 * Exception for authentication-related errors.
 */
class AuthenticationException extends NestException
{
    public function __construct(string $message = 'Authentication failed', int $code = 401, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
