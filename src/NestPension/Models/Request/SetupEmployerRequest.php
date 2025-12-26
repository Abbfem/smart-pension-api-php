<?php

declare(strict_types=1);

namespace NestPension\Models\Request;

/**
 * Request model for setting up a new employer in NEST pension scheme.
 */
class SetupEmployerRequest
{
    private ?string $acknowledgementId = null;
    private ?string $employerName = null;
    private ?string $payeReference = null;
    private ?string $companiesHouseNumber = null;
    private array $address = [];
    private array $contactDetails = [];
    private array $groups = [];
    private array $paymentSources = [];
    private ?string $stagingDate = null;
    private ?string $firstContributionDueDate = null;

    public function __construct(?string $acknowledgementId = null)
    {
        $this->acknowledgementId = $acknowledgementId ?: 'SEU_' . date('YmdHis') . '_' . uniqid();
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
     * Set employer name.
     */
    public function setEmployerName(string $employerName): self
    {
        $this->employerName = $employerName;
        return $this;
    }

    /**
     * Set PAYE reference.
     */
    public function setPayeReference(string $payeReference): self
    {
        $this->payeReference = $payeReference;
        return $this;
    }

    /**
     * Set Companies House number.
     */
    public function setCompaniesHouseNumber(?string $companiesHouseNumber): self
    {
        $this->companiesHouseNumber = $companiesHouseNumber;
        return $this;
    }

    /**
     * Set address.
     */
    public function setAddress(array $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Set contact details.
     */
    public function setContactDetails(array $contactDetails): self
    {
        $this->contactDetails = $contactDetails;
        return $this;
    }

    /**
     * Add a group.
     */
    public function addGroup(array $groupData): self
    {
        $this->groups[] = $groupData;
        return $this;
    }

    /**
     * Set groups.
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * Add a payment source.
     */
    public function addPaymentSource(array $paymentSource): self
    {
        $this->paymentSources[] = $paymentSource;
        return $this;
    }

    /**
     * Set payment sources.
     */
    public function setPaymentSources(array $paymentSources): self
    {
        $this->paymentSources = $paymentSources;
        return $this;
    }

    /**
     * Set staging date.
     */
    public function setStagingDate(?string $stagingDate): self
    {
        $this->stagingDate = $stagingDate;
        return $this;
    }

    /**
     * Set first contribution due date.
     */
    public function setFirstContributionDueDate(?string $firstContributionDueDate): self
    {
        $this->firstContributionDueDate = $firstContributionDueDate;
        return $this;
    }

    // Getters

    public function getAcknowledgementId(): ?string
    {
        return $this->acknowledgementId;
    }

    public function getEmployerName(): ?string
    {
        return $this->employerName;
    }

    public function getPayeReference(): ?string
    {
        return $this->payeReference;
    }

    public function getCompaniesHouseNumber(): ?string
    {
        return $this->companiesHouseNumber;
    }

    public function getAddress(): array
    {
        return $this->address;
    }

    public function getContactDetails(): array
    {
        return $this->contactDetails;
    }

    public function getGroups(): array
    {
        return $this->groups;
    }

    public function getPaymentSources(): array
    {
        return $this->paymentSources;
    }

    public function getStagingDate(): ?string
    {
        return $this->stagingDate;
    }

    public function getFirstContributionDueDate(): ?string
    {
        return $this->firstContributionDueDate;
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
            'EmployerDetails' => [
                'EmployerName' => $this->employerName,
                'PayeReference' => $this->payeReference,
            ],
        ];

        if ($this->companiesHouseNumber) {
            $data['EmployerDetails']['CompaniesHouseNumber'] = $this->companiesHouseNumber;
        }

        if (!empty($this->address)) {
            $data['EmployerDetails']['Address'] = $this->address;
        }

        if (!empty($this->contactDetails)) {
            $data['ContactDetails'] = $this->contactDetails;
        }

        if ($this->stagingDate) {
            $data['StagingDate'] = $this->stagingDate;
        }

        if ($this->firstContributionDueDate) {
            $data['FirstContributionDueDate'] = $this->firstContributionDueDate;
        }

        if (!empty($this->groups)) {
            $data['Groups'] = [];
            foreach ($this->groups as $group) {
                $data['Groups'][] = ['Group' => $group];
            }
        }

        if (!empty($this->paymentSources)) {
            $data['PaymentSources'] = [];
            foreach ($this->paymentSources as $paymentSource) {
                $data['PaymentSources'][] = ['PaymentSource' => $paymentSource];
            }
        }

        return $data;
    }
}
