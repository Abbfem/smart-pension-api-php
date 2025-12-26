<?php

namespace PeoplesPension\Models;

/**
 * Contribution Error model.
 * 
 * Represents a validation error from a contribution submission.
 */
class ContributionError
{
    /**
     * Error categories.
     */
    public const CATEGORIES = [
        'Authorisation',
        'System',
        'File',
        'HeaderValidation',
        'DetailsRecord',
        'TrailerValidation',
    ];

    public function __construct(
        public readonly string $code,
        public readonly string $title,
        public readonly ?string $uniqueId = null,
        public readonly ?string $providedValue = null,
        public readonly ?string $expectedValue = null,
        public readonly ?string $employeeIndex = null,
        public readonly ?string $category = null,
        public readonly ?string $aboutLink = null
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $meta = $data['meta'] ?? [];
        $links = $data['links'] ?? [];

        return new self(
            code: $data['code'],
            title: $data['title'],
            uniqueId: $meta['uniqueId'] ?? null,
            providedValue: $meta['providedValue'] ?? null,
            expectedValue: $meta['expectedValue'] ?? null,
            employeeIndex: $meta['employeeIndex'] ?? null,
            category: $meta['category'] ?? null,
            aboutLink: $links['about'] ?? null
        );
    }
}
