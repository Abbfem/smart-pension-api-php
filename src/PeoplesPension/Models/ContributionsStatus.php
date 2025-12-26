<?php

namespace PeoplesPension\Models;

/**
 * Contributions Status model.
 * 
 * Represents the processing status of a submitted contribution.
 */
class ContributionsStatus
{
    public function __construct(
        public readonly string $id,
        public readonly bool $received,
        public readonly bool $validating,
        public readonly bool $accepted,
        public readonly bool $failed,
        public readonly bool $processed,
        public readonly ?string $nextPollAfter = null,
        public readonly ?string $selfLink = null,
        public readonly ?string $errorsLink = null
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? $data;
        $links = $data['links'] ?? [];

        return new self(
            id: $data['id'] ?? '',
            received: $attributes['received'] ?? false,
            validating: $attributes['validating'] ?? false,
            accepted: $attributes['accepted'] ?? false,
            failed: $attributes['failed'] ?? false,
            processed: $attributes['processed'] ?? false,
            nextPollAfter: $attributes['nextPollAfter'] ?? null,
            selfLink: $links['self'] ?? null,
            errorsLink: $links['errors'] ?? null
        );
    }

    /**
     * Check if processing is complete (either accepted or failed).
     */
    public function isComplete(): bool
    {
        return $this->accepted || $this->failed || $this->processed;
    }

    /**
     * Check if there are validation errors.
     */
    public function hasErrors(): bool
    {
        return $this->failed && $this->errorsLink !== null;
    }
}
