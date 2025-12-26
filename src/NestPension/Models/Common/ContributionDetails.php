<?php

declare(strict_types=1);

namespace NestPension\Models\Common;

/**
 * Contribution details model for NEST pension API.
 * Based on NEST ContributionRates namespace (xmlns:cnr).
 */
class ContributionDetails
{
    private ?string $memberId = null;
    private ?string $employeeId = null;
    private ?float $pensionableEarnings = null;
    private ?float $employerContribution = null;
    private ?float $memberContribution = null;
    private ?float $totalContribution = null;
    private ?string $contributionPeriodStart = null;
    private ?string $contributionPeriodEnd = null;
    private ?string $payrollPeriod = null;
    private ?string $contributionType = null;
    private ?string $contributionBasis = null;
    private ?float $contributionRate = null;
    private ?bool $isArrears = null;
    private ?string $paymentMethod = null;
    private array $additionalData = [];

    // Valid contribution types
    public const VALID_CONTRIBUTION_TYPES = ['Regular', 'Arrears', 'Adjustment', 'Refund'];

    // Valid contribution basis
    public const VALID_CONTRIBUTION_BASIS = ['QualifyingEarnings', 'BasicSalary', 'TotalEarnings', 'Custom'];

    // Valid payment methods
    public const VALID_PAYMENT_METHODS = ['BACS', 'FasterPayments', 'Cheque', 'DirectDebit'];

    /**
     * Set member ID.
     */
    public function setMemberId(?string $memberId): self
    {
        $this->memberId = $memberId;
        return $this;
    }

    /**
     * Set employee ID.
     */
    public function setEmployeeId(?string $employeeId): self
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    /**
     * Set pensionable earnings.
     */
    public function setPensionableEarnings(?float $pensionableEarnings): self
    {
        $this->pensionableEarnings = $pensionableEarnings;
        return $this;
    }

    /**
     * Set employer contribution.
     */
    public function setEmployerContribution(?float $employerContribution): self
    {
        $this->employerContribution = $employerContribution;
        return $this;
    }

    /**
     * Set member contribution.
     */
    public function setMemberContribution(?float $memberContribution): self
    {
        $this->memberContribution = $memberContribution;
        return $this;
    }

    /**
     * Set total contribution (calculated if not provided).
     */
    public function setTotalContribution(?float $totalContribution): self
    {
        $this->totalContribution = $totalContribution;
        return $this;
    }

    /**
     * Set contribution period start date.
     */
    public function setContributionPeriodStart(?string $contributionPeriodStart): self
    {
        $this->contributionPeriodStart = $contributionPeriodStart;
        return $this;
    }

    /**
     * Set contribution period end date.
     */
    public function setContributionPeriodEnd(?string $contributionPeriodEnd): self
    {
        $this->contributionPeriodEnd = $contributionPeriodEnd;
        return $this;
    }

    /**
     * Set payroll period.
     */
    public function setPayrollPeriod(?string $payrollPeriod): self
    {
        $this->payrollPeriod = $payrollPeriod;
        return $this;
    }

    /**
     * Set contribution type.
     */
    public function setContributionType(?string $contributionType): self
    {
        $this->contributionType = $contributionType;
        return $this;
    }

    /**
     * Set contribution basis.
     */
    public function setContributionBasis(?string $contributionBasis): self
    {
        $this->contributionBasis = $contributionBasis;
        return $this;
    }

    /**
     * Set contribution rate (as percentage).
     */
    public function setContributionRate(?float $contributionRate): self
    {
        $this->contributionRate = $contributionRate;
        return $this;
    }

    /**
     * Set arrears flag.
     */
    public function setIsArrears(?bool $isArrears): self
    {
        $this->isArrears = $isArrears;
        return $this;
    }

    /**
     * Set payment method.
     */
    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * Set additional data.
     */
    public function setAdditionalData(array $additionalData): self
    {
        $this->additionalData = $additionalData;
        return $this;
    }

    // Getters

    public function getMemberId(): ?string
    {
        return $this->memberId;
    }

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function getPensionableEarnings(): ?float
    {
        return $this->pensionableEarnings;
    }

    public function getEmployerContribution(): ?float
    {
        return $this->employerContribution;
    }

    public function getMemberContribution(): ?float
    {
        return $this->memberContribution;
    }

    public function getTotalContribution(): ?float
    {
        if ($this->totalContribution !== null) {
            return $this->totalContribution;
        }

        if ($this->employerContribution !== null && $this->memberContribution !== null) {
            return $this->employerContribution + $this->memberContribution;
        }

        return null;
    }

    public function getContributionPeriodStart(): ?string
    {
        return $this->contributionPeriodStart;
    }

    public function getContributionPeriodEnd(): ?string
    {
        return $this->contributionPeriodEnd;
    }

    public function getPayrollPeriod(): ?string
    {
        return $this->payrollPeriod;
    }

    public function getContributionType(): ?string
    {
        return $this->contributionType;
    }

    public function getContributionBasis(): ?string
    {
        return $this->contributionBasis;
    }

    public function getContributionRate(): ?float
    {
        return $this->contributionRate;
    }

    public function getIsArrears(): ?bool
    {
        return $this->isArrears;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function getAdditionalData(): array
    {
        return $this->additionalData;
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $data = [
            'MemberId' => $this->memberId,
            'EmployeeId' => $this->employeeId,
            'PensionableEarnings' => $this->pensionableEarnings,
            'EmployerContribution' => $this->employerContribution,
            'MemberContribution' => $this->memberContribution,
            'TotalContribution' => $this->getTotalContribution(),
            'ContributionPeriodStart' => $this->contributionPeriodStart,
            'ContributionPeriodEnd' => $this->contributionPeriodEnd,
            'PayrollPeriod' => $this->payrollPeriod,
            'ContributionType' => $this->contributionType,
            'ContributionBasis' => $this->contributionBasis,
            'ContributionRate' => $this->contributionRate,
            'PaymentMethod' => $this->paymentMethod,
        ];

        if ($this->isArrears !== null) {
            $data['IsArrears'] = $this->isArrears ? 'true' : 'false';
        }

        return array_filter(array_merge($data, $this->additionalData), fn($value) => $value !== null);
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $contribution = new self();

        if (isset($data['MemberId'])) {
            $contribution->setMemberId($data['MemberId']);
        }
        if (isset($data['EmployeeId'])) {
            $contribution->setEmployeeId($data['EmployeeId']);
        }
        if (isset($data['PensionableEarnings'])) {
            $contribution->setPensionableEarnings((float)$data['PensionableEarnings']);
        }
        if (isset($data['EmployerContribution'])) {
            $contribution->setEmployerContribution((float)$data['EmployerContribution']);
        }
        if (isset($data['MemberContribution'])) {
            $contribution->setMemberContribution((float)$data['MemberContribution']);
        }
        if (isset($data['TotalContribution'])) {
            $contribution->setTotalContribution((float)$data['TotalContribution']);
        }
        if (isset($data['ContributionPeriodStart'])) {
            $contribution->setContributionPeriodStart($data['ContributionPeriodStart']);
        }
        if (isset($data['ContributionPeriodEnd'])) {
            $contribution->setContributionPeriodEnd($data['ContributionPeriodEnd']);
        }
        if (isset($data['PayrollPeriod'])) {
            $contribution->setPayrollPeriod($data['PayrollPeriod']);
        }
        if (isset($data['ContributionType'])) {
            $contribution->setContributionType($data['ContributionType']);
        }
        if (isset($data['ContributionBasis'])) {
            $contribution->setContributionBasis($data['ContributionBasis']);
        }
        if (isset($data['ContributionRate'])) {
            $contribution->setContributionRate((float)$data['ContributionRate']);
        }
        if (isset($data['IsArrears'])) {
            $contribution->setIsArrears($data['IsArrears'] === 'true' || $data['IsArrears'] === true);
        }
        if (isset($data['PaymentMethod'])) {
            $contribution->setPaymentMethod($data['PaymentMethod']);
        }

        return $contribution;
    }
}
