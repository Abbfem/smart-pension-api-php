<?php

declare(strict_types=1);

namespace NestPension\Exceptions;

use Exception;

/**
 * Base exception class for NEST Pension library.
 */
class NestException extends Exception
{
    protected array $context = [];

    public function __construct(string $message = '', int $code = 0, ?Exception $previous = null, array $context = [])
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    /**
     * Get additional context information.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Set additional context information.
     */
    public function setContext(array $context): self
    {
        $this->context = $context;
        return $this;
    }

    /**
     * Add context item.
     */
    public function addContext(string $key, mixed $value): self
    {
        $this->context[$key] = $value;
        return $this;
    }

    /**
     * Get formatted error message with context.
     */
    public function getFormattedMessage(): string
    {
        $message = $this->getMessage();

        if (!empty($this->context)) {
            $message .= ' Context: ' . json_encode($this->context);
        }

        return $message;
    }
}
