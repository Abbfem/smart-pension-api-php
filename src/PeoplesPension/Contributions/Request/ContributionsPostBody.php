<?php

namespace PeoplesPension\Contributions\Request;

use PeoplesPension\Request\PostBody;
use PeoplesPension\Exceptions\InvalidPostBodyException;
use PeoplesPension\Models\DateRange;
use PeoplesPension\Models\EmployeeContribution;

/**
 * Post body for submitting contributions.
 */
class ContributionsPostBody implements PostBody
{
    private string $accountId;
    private DateRange $payReferencePeriod;
    /** @var EmployeeContribution[] */
    private array $employees;
    private float $total;

    /**
     * @param string $accountId The admin account's unique identifier
     * @param DateRange $payReferencePeriod The pay reference period for contributions
     * @param EmployeeContribution[] $employees Array of employee contributions
     * @param float $total The total value of contributions in the submission
     */
    public function __construct(
        string $accountId,
        DateRange $payReferencePeriod,
        array $employees,
        float $total
    ) {
        $this->accountId = $accountId;
        $this->payReferencePeriod = $payReferencePeriod;
        $this->employees = $employees;
        $this->total = $total;
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $employees = [];
        foreach ($data['employees'] as $employee) {
            $employees[] = EmployeeContribution::fromArray($employee);
        }

        return new self(
            accountId: $data['accountId'],
            payReferencePeriod: DateRange::fromArray($data['payReferencePeriod']),
            employees: $employees,
            total: (float) $data['total']
        );
    }

    /**
     * Validate the post body.
     *
     * @throws InvalidPostBodyException
     */
    public function validate(): void
    {
        if (empty($this->accountId)) {
            throw new InvalidPostBodyException('Account ID is required.');
        }

        if ($this->payReferencePeriod->start === null) {
            throw new InvalidPostBodyException('Pay reference period start date is required.');
        }

        if ($this->payReferencePeriod->end === null) {
            throw new InvalidPostBodyException('Pay reference period end date is required.');
        }

        if (empty($this->employees)) {
            throw new InvalidPostBodyException('At least one employee contribution is required.');
        }

        // Validate total matches sum of contributions
        $calculatedTotal = 0;
        foreach ($this->employees as $employee) {
            $calculatedTotal += $employee->employerContributionAmount + $employee->employeeContributionAmount;
        }

        // Allow for floating point precision issues
        if (abs($calculatedTotal - $this->total) > 0.01) {
            throw new InvalidPostBodyException(
                "Total ({$this->total}) does not match sum of contributions ({$calculatedTotal})."
            );
        }
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $employees = [];
        foreach ($this->employees as $employee) {
            $employees[] = $employee->toArray();
        }

        return [
            'data' => [
                'type' => 'contributions',
                'attributes' => [
                    'accountId' => $this->accountId,
                    'payReferencePeriod' => $this->payReferencePeriod->toArray(),
                    'employees' => $employees,
                    'total' => round($this->total, 2),
                ],
            ],
        ];
    }

    /**
     * Add an employee contribution.
     */
    public function addEmployee(EmployeeContribution $employee): self
    {
        $this->employees[] = $employee;
        return $this;
    }

    /**
     * Set the total (auto-calculates if not set).
     */
    public function calculateTotal(): self
    {
        $this->total = 0;
        foreach ($this->employees as $employee) {
            $this->total += $employee->getTotalContribution();
        }
        return $this;
    }
}
