<?php

namespace PeoplesPension\Models;

/**
 * Account Summary model.
 * 
 * Contains summary information about an admin account.
 */
class AccountSummary
{
    public function __construct(
        public readonly string $id,
        public readonly string $accountName,
        public readonly bool $isSupported,
        public readonly ?string $reason = null,
        public readonly bool $hasContractualEnrolment = false,
        public readonly bool $hasEmployeeAccidentOrLifeCover = false,
        public readonly bool $isEasyBuild = false,
        public readonly bool $isUsingAssessment = false,
        public readonly ?string $selfLink = null
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? [];
        $api = $attributes['api'] ?? [];
        $links = $data['links'] ?? [];

        return new self(
            id: $data['id'],
            accountName: $attributes['accountName'],
            isSupported: $api['isSupported'] ?? true,
            reason: $api['reason'] ?? null,
            hasContractualEnrolment: $attributes['hasContractualEnrolment'] ?? false,
            hasEmployeeAccidentOrLifeCover: $attributes['hasEmployeeAccidentOrLifeCover'] ?? false,
            isEasyBuild: $attributes['isEasyBuild'] ?? false,
            isUsingAssessment: $attributes['isUsingAssessment'] ?? false,
            selfLink: $links['self'] ?? null
        );
    }
}
