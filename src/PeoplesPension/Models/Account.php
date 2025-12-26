<?php

namespace PeoplesPension\Models;

/**
 * Account model.
 * 
 * Contains detailed information about an admin account.
 */
class Account
{
    /**
     * Allowed PRP (Pay Reference Period) frequency values.
     */
    public const PRP_FREQUENCY = [
        'Weekly',
        'Monthly',
        'Fortnightly',
        'Four Weekly',
    ];

    /**
     * Allowed payroll frequency values.
     */
    public const PAYROLL_FREQUENCY = [
        'Weekly',
        'Monthly',
        'Fortnightly',
        'Four Weekly',
        'Weekly-Monthly',
    ];

    /**
     * Allowed tax basis values.
     */
    public const TAX_BASIS = [
        'Gross',
        'Net',
    ];

    public function __construct(
        public readonly string $id,
        public readonly string $accountName,
        public readonly string $prpFrequency,
        public readonly string $payrollFrequency,
        public readonly string $companyName,
        public readonly string $stagingDate,
        public readonly string $taxBasis,
        public readonly ?DateRange $nextPayReferencePeriod = null,
        /** @var WorkerGroup[] */
        public readonly array $workerGroups = [],
        public readonly ?string $selfLink = null,
        public readonly ?string $optOutsLink = null
    ) {}

    /**
     * Create from API response array.
     */
    public static function fromArray(array $data): self
    {
        $attributes = $data['attributes'] ?? $data;
        $links = $data['links'] ?? [];

        $workerGroups = [];
        if (isset($attributes['workerGroups'])) {
            foreach ($attributes['workerGroups'] as $wg) {
                $workerGroups[] = WorkerGroup::fromArray($wg);
            }
        }

        return new self(
            id: $data['id'] ?? '',
            accountName: $attributes['accountName'],
            prpFrequency: $attributes['prpFrequency'],
            payrollFrequency: $attributes['payrollFrequency'],
            companyName: $attributes['companyName'],
            stagingDate: $attributes['stagingDate'],
            taxBasis: $attributes['taxBasis'],
            nextPayReferencePeriod: isset($attributes['nextPayReferencePeriod']) 
                ? DateRange::fromArray($attributes['nextPayReferencePeriod']) 
                : null,
            workerGroups: $workerGroups,
            selfLink: $links['self'] ?? null,
            optOutsLink: $links['opt-outs'] ?? null
        );
    }

    /**
     * Get a worker group by ID.
     */
    public function getWorkerGroup(string $id): ?WorkerGroup
    {
        foreach ($this->workerGroups as $workerGroup) {
            if ($workerGroup->id === $id) {
                return $workerGroup;
            }
        }
        return null;
    }
}
