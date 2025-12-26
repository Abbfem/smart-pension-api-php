<?php

namespace PeoplesPension\Models;

/**
 * Worker Group model.
 * 
 * Represents a group of workers with specific contribution settings.
 */
class WorkerGroup
{
    /**
     * Allowed earnings basis values.
     */
    public const EARNINGS_BASIS = [
        'Set 1 Pensionable Earnings',
        'Set 2 Pensionable Earnings',
        'Set 3 Total Earnings',
        'Qualifying Earnings',
        'Fixed Amount',
    ];

    public function __construct(
        public readonly string $id,
        public readonly string $description,
        public readonly string $earningsBasis,
        public readonly float $employeeContributionAmount,
        public readonly float $employeeContributionPercent,
        public readonly float $employerContributionAmount,
        public readonly float $employerContributionPercent,
        public readonly ?DateRange $effective = null
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            description: $data['description'],
            earningsBasis: $data['earningsBasis'],
            employeeContributionAmount: (float) ($data['employeeContributionAmount'] ?? 0),
            employeeContributionPercent: (float) ($data['employeeContributionPercent'] ?? 0),
            employerContributionAmount: (float) ($data['employerContributionAmount'] ?? 0),
            employerContributionPercent: (float) ($data['employerContributionPercent'] ?? 0),
            effective: isset($data['effective']) ? DateRange::fromArray($data['effective']) : null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $result = [
            'id' => $this->id,
            'description' => $this->description,
            'earningsBasis' => $this->earningsBasis,
            'employeeContributionAmount' => $this->employeeContributionAmount,
            'employeeContributionPercent' => $this->employeeContributionPercent,
            'employerContributionAmount' => $this->employerContributionAmount,
            'employerContributionPercent' => $this->employerContributionPercent,
        ];

        if ($this->effective !== null) {
            $result['effective'] = $this->effective->toArray();
        }

        return $result;
    }
}
