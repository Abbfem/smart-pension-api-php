<?php

declare(strict_types=1);

namespace NestPension\Models\Request;

/**
 * Request model for approving payment to NEST pension scheme.
 */
class ApprovePaymentRequest
{
    private ?string $acknowledgementId = null;
    private ?string $paymentSourceId = null;
    private ?float $amount = null;
    private ?string $paymentDate = null;
    private ?string $paymentReference = null;
    private array $scheduleIds = [];

    public function __construct(?string $acknowledgementId = null)
    {
        $this->acknowledgementId = $acknowledgementId ?: 'APR_' . date('YmdHis') . '_' . uniqid();
    }

    /**
     * Set acknowledgement ID.
     */
    public function setAcknowledgementId(string $acknowledgementId): self
    {
        $this->acknowledgementId = $acknowledgementId;
        return $this;
    }

    /**
     * Set payment source ID.
     */
    public function setPaymentSourceId(string $paymentSourceId): self
    {
        $this->paymentSourceId = $paymentSourceId;
        return $this;
    }

    /**
     * Set payment amount.
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * Set payment date.
     */
    public function setPaymentDate(string $paymentDate): self
    {
        $this->paymentDate = $paymentDate;
        return $this;
    }

    /**
     * Set payment reference.
     */
    public function setPaymentReference(?string $paymentReference): self
    {
        $this->paymentReference = $paymentReference;
        return $this;
    }

    /**
     * Add schedule ID.
     */
    public function addScheduleId(string $scheduleId): self
    {
        $this->scheduleIds[] = $scheduleId;
        return $this;
    }

    /**
     * Set schedule IDs.
     */
    public function setScheduleIds(array $scheduleIds): self
    {
        $this->scheduleIds = $scheduleIds;
        return $this;
    }

    // Getters

    public function getAcknowledgementId(): ?string
    {
        return $this->acknowledgementId;
    }

    public function getPaymentSourceId(): ?string
    {
        return $this->paymentSourceId;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function getPaymentDate(): ?string
    {
        return $this->paymentDate;
    }

    public function getPaymentReference(): ?string
    {
        return $this->paymentReference;
    }

    public function getScheduleIds(): array
    {
        return $this->scheduleIds;
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
            'PaymentDetails' => [
                'PaymentSourceId' => $this->paymentSourceId,
                'Amount' => $this->amount,
                'PaymentDate' => $this->paymentDate,
            ],
        ];

        if ($this->paymentReference) {
            $data['PaymentDetails']['PaymentReference'] = $this->paymentReference;
        }

        if (!empty($this->scheduleIds)) {
            $data['Schedules'] = [];
            foreach ($this->scheduleIds as $scheduleId) {
                $data['Schedules'][] = ['ScheduleId' => $scheduleId];
            }
        }

        return $data;
    }
}
