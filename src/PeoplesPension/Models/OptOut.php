<?php

namespace PeoplesPension\Models;

/**
 * Opt Out model.
 * 
 * Represents an employee who has opted out of the pension scheme.
 */
class OptOut
{
    /**
     * Allowed refund status values.
     */
    public const REFUND_STATUS = [
        'No Refund',
        'Processing',
        'Processed',
    ];

    /**
     * Allowed opt out channel values.
     */
    public const OPT_OUT_CHANNEL = [
        'Online',
        'Telephone',
        'Paper',
    ];

    public function __construct(
        public readonly string $uniqueId,
        public readonly string $forename,
        public readonly string $surname,
        public readonly ?string $niNumber = null,
        public readonly ?string $optOutDate = null,
        public readonly ?string $refundStatus = null,
        public readonly ?string $optOutChannel = null,
        public readonly float $employeeRefund = 0,
        public readonly float $employerRefund = 0,
        public readonly bool $entitledToRefund = false
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            uniqueId: $data['uniqueId'],
            forename: $data['forename'],
            surname: $data['surname'],
            niNumber: $data['niNumber'] ?? null,
            optOutDate: $data['optOutDate'] ?? null,
            refundStatus: $data['refundStatus'] ?? null,
            optOutChannel: $data['optOutChannel'] ?? null,
            employeeRefund: (float) ($data['employeeRefund'] ?? 0),
            employerRefund: (float) ($data['employerRefund'] ?? 0),
            entitledToRefund: $data['entitledToRefund'] ?? false
        );
    }

    /**
     * Get total refund amount.
     */
    public function getTotalRefund(): float
    {
        return $this->employeeRefund + $this->employerRefund;
    }
}
