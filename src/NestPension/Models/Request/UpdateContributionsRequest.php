<?php

declare(strict_types=1);

namespace NestPension\Models\Request;

use NestPension\Models\Common\ContributionDetails;

/**
 * Request model for updating contributions to NEST pension scheme.
 */
class UpdateContributionsRequest
{
    private array $contributions = [];
    private ?string $payrollPeriod = null;
    private ?string $paymentSource = null;
    private ?string $earnPeriodStart = null;
    private ?string $earnPeriodEnd = null;
    private ?string $acknowledgementId = null;

    public function __construct(?string $acknowledgementId = null)
    {
        $this->acknowledgementId = $acknowledgementId ?: 'UCR_' . date('YmdHis') . '_' . uniqid();
    }

    /**
     * Add a contribution entry.
     */
    public function addContribution(array|ContributionDetails $contributionData): self
    {
        if ($contributionData instanceof ContributionDetails) {
            $this->contributions[] = $contributionData->toArray();
        } else {
            $this->contributions[] = $contributionData;
        }
        return $this;
    }

    /**
     * Set payroll period.
     */
    public function setPayrollPeriod(string $payrollPeriod): self
    {
        $this->payrollPeriod = $payrollPeriod;
        return $this;
    }

    /**
     * Set payment source.
     */
    public function setPaymentSource(string $paymentSource): self
    {
        $this->paymentSource = $paymentSource;
        return $this;
    }

    /**
     * Set earning period start date.
     */
    public function setEarnPeriodStart(string $earnPeriodStart): self
    {
        $this->earnPeriodStart = $earnPeriodStart;
        return $this;
    }

    /**
     * Set earning period end date.
     */
    public function setEarnPeriodEnd(string $earnPeriodEnd): self
    {
        $this->earnPeriodEnd = $earnPeriodEnd;
        return $this;
    }

    /**
     * Set acknowledgement ID.
     */
    public function setAcknowledgementId(string $acknowledgementId): self
    {
        $this->acknowledgementId = $acknowledgementId;
        return $this;
    }

    // Getters

    public function getContributions(): array
    {
        return $this->contributions;
    }

    public function getPayrollPeriod(): ?string
    {
        return $this->payrollPeriod;
    }

    public function getPaymentSource(): ?string
    {
        return $this->paymentSource;
    }

    public function getEarnPeriodStart(): ?string
    {
        return $this->earnPeriodStart;
    }

    public function getEarnPeriodEnd(): ?string
    {
        return $this->earnPeriodEnd;
    }

    public function getAcknowledgementId(): ?string
    {
        return $this->acknowledgementId;
    }

    /**
     * Get total contribution amount.
     */
    public function getTotalAmount(): float
    {
        $total = 0.0;
        foreach ($this->contributions as $contribution) {
            $total += ($contribution['EmployerContribution'] ?? 0) + ($contribution['MemberContribution'] ?? 0);
        }
        return $total;
    }

    /**
     * Get contribution count.
     */
    public function getContributionCount(): int
    {
        return count($this->contributions);
    }

    /**
     * Convert to array for XML serialization.
     */
    public function toArray(): array
    {
        $data = [
            'Header' => [
                'AcknowledgementId' => $this->acknowledgementId,
                'TimeStamp' => date('c'),
            ],
        ];

        if ($this->payrollPeriod) {
            $data['PayrollPeriod'] = $this->payrollPeriod;
        }

        if ($this->paymentSource) {
            $data['PaymentSource'] = $this->paymentSource;
        }

        if ($this->earnPeriodStart) {
            $data['EarnPeriodStart'] = $this->earnPeriodStart;
        }

        if ($this->earnPeriodEnd) {
            $data['EarnPeriodEnd'] = $this->earnPeriodEnd;
        }

        $data['Contributions'] = [];
        foreach ($this->contributions as $contribution) {
            $data['Contributions'][] = ['Contribution' => $contribution];
        }

        return $data;
    }
}
