<?php

declare(strict_types=1);

namespace NestPension\Models\Common;

/**
 * Group model for NEST pension API.
 * Based on NEST Group namespace (xmlns:grp).
 */
class Group
{
    private ?string $groupId = null;
    private ?string $groupName = null;
    private ?float $employerContributionRate = null;
    private ?float $memberContributionRate = null;
    private ?string $paymentSourceId = null;
    private ?string $pensionCalculationBasis = null;
    private ?string $taxReliefMethod = null;
    private ?bool $isDefault = null;

    // Valid pension calculation basis
    public const VALID_CALCULATION_BASIS = ['QualifyingEarnings', 'BasicSalary', 'TotalEarnings', 'Custom'];

    // Valid tax relief methods
    public const VALID_TAX_RELIEF_METHODS = ['NetPay', 'ReliefAtSource'];

    /**
     * Set group ID.
     */
    public function setGroupId(string $groupId): self
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Set group name.
     */
    public function setGroupName(string $groupName): self
    {
        $this->groupName = $groupName;
        return $this;
    }

    /**
     * Set employer contribution rate (percentage).
     */
    public function setEmployerContributionRate(float $rate): self
    {
        $this->employerContributionRate = $rate;
        return $this;
    }

    /**
     * Set member contribution rate (percentage).
     */
    public function setMemberContributionRate(float $rate): self
    {
        $this->memberContributionRate = $rate;
        return $this;
    }

    /**
     * Set payment source ID.
     */
    public function setPaymentSourceId(?string $paymentSourceId): self
    {
        $this->paymentSourceId = $paymentSourceId;
        return $this;
    }

    /**
     * Set pension calculation basis.
     */
    public function setPensionCalculationBasis(?string $basis): self
    {
        $this->pensionCalculationBasis = $basis;
        return $this;
    }

    /**
     * Set tax relief method.
     */
    public function setTaxReliefMethod(?string $method): self
    {
        $this->taxReliefMethod = $method;
        return $this;
    }

    /**
     * Set if this is the default group.
     */
    public function setIsDefault(?bool $isDefault): self
    {
        $this->isDefault = $isDefault;
        return $this;
    }

    // Getters

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function getGroupName(): ?string
    {
        return $this->groupName;
    }

    public function getEmployerContributionRate(): ?float
    {
        return $this->employerContributionRate;
    }

    public function getMemberContributionRate(): ?float
    {
        return $this->memberContributionRate;
    }

    public function getPaymentSourceId(): ?string
    {
        return $this->paymentSourceId;
    }

    public function getPensionCalculationBasis(): ?string
    {
        return $this->pensionCalculationBasis;
    }

    public function getTaxReliefMethod(): ?string
    {
        return $this->taxReliefMethod;
    }

    public function getIsDefault(): ?bool
    {
        return $this->isDefault;
    }

    /**
     * Get total contribution rate.
     */
    public function getTotalContributionRate(): ?float
    {
        if ($this->employerContributionRate === null && $this->memberContributionRate === null) {
            return null;
        }

        return ($this->employerContributionRate ?? 0) + ($this->memberContributionRate ?? 0);
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $data = [
            'GroupId' => $this->groupId,
            'GroupName' => $this->groupName,
            'EmployerContributionRate' => $this->employerContributionRate,
            'MemberContributionRate' => $this->memberContributionRate,
            'PaymentSourceId' => $this->paymentSourceId,
            'PensionCalculationBasis' => $this->pensionCalculationBasis,
            'TaxReliefMethod' => $this->taxReliefMethod,
        ];

        if ($this->isDefault !== null) {
            $data['IsDefault'] = $this->isDefault ? 'true' : 'false';
        }

        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $group = new self();

        if (isset($data['GroupId'])) {
            $group->setGroupId($data['GroupId']);
        }
        if (isset($data['GroupName'])) {
            $group->setGroupName($data['GroupName']);
        }
        if (isset($data['EmployerContributionRate'])) {
            $group->setEmployerContributionRate((float)$data['EmployerContributionRate']);
        }
        if (isset($data['MemberContributionRate'])) {
            $group->setMemberContributionRate((float)$data['MemberContributionRate']);
        }
        if (isset($data['PaymentSourceId'])) {
            $group->setPaymentSourceId($data['PaymentSourceId']);
        }
        if (isset($data['PensionCalculationBasis'])) {
            $group->setPensionCalculationBasis($data['PensionCalculationBasis']);
        }
        if (isset($data['TaxReliefMethod'])) {
            $group->setTaxReliefMethod($data['TaxReliefMethod']);
        }
        if (isset($data['IsDefault'])) {
            $group->setIsDefault($data['IsDefault'] === 'true' || $data['IsDefault'] === true);
        }

        return $group;
    }
}
