<?php

declare(strict_types=1);

namespace NestPension\Exceptions;

/**
 * Exception for validation-related errors.
 */
class ValidationException extends NestException
{
    private array $errors;

    public function __construct(string $message = 'Validation failed', array $errors = [], int $code = 0, ?\Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->errors = $errors;
    }

    /**
     * Get validation errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Add validation error.
     */
    public function addError(string $field, string $message): self
    {
        $this->errors[$field] = $message;
        return $this;
    }

    /**
     * Check if there are any errors.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get formatted error messages.
     */
    public function getFormattedErrors(): string
    {
        $messages = [];
        foreach ($this->errors as $field => $message) {
            $messages[] = "{$field}: {$message}";
        }
        return implode('; ', $messages);
    }
}
