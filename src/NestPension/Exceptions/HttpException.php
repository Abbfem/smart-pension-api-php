<?php

declare(strict_types=1);

namespace NestPension\Exceptions;

/**
 * Exception for HTTP-related errors.
 */
class HttpException extends NestException
{
    private ?int $httpStatusCode;

    public function __construct(string $message = '', int $code = 0, ?\Exception $previous = null, ?int $httpStatusCode = null)
    {
        parent::__construct($message, $code, $previous);
        $this->httpStatusCode = $httpStatusCode ?? $code;
    }

    /**
     * Get the HTTP status code.
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }
}
