<?php

namespace PeoplesPension\Exceptions;

/**
 * Exception thrown when API returns an error response.
 */
class ApiException extends PeoplesPensionException
{
    private array $errors;
    private int $statusCode;

    public function __construct(string $message, int $statusCode, array $errors = [])
    {
        parent::__construct($message);
        $this->statusCode = $statusCode;
        $this->errors = $errors;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
