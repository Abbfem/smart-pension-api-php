<?php

namespace PeoplesPension\Models;

/**
 * Employee Contribution model.
 * 
 * Represents an employee's contribution data for a pay reference period.
 */
class EmployeeContribution
{
    /**
     * Allowed title values.
     */
    public const TITLES = ['Mr', 'Mrs', 'Miss', 'Ms', 'Mx', 'Dr', 'Sir'];

    /**
     * Allowed gender values.
     */
    public const GENDERS = ['M', 'F'];

    /**
     * Allowed auto enrolment status values.
     */
    public const AUTO_ENROLMENT_STATUS = [
        'Eligible',
        'Non Eligible',
        'Entitled',
        'Contractual Enrolment',
        'Not Known',
        'Already In Qualifying Scheme',
        'Not Applicable',
    ];

    /**
     * Allowed partial contribution reason values.
     */
    public const PARTIAL_CONTRIBUTION_REASONS = [
        'Employee Transfer',
    ];

    public function __construct(
        public readonly string $title,
        public readonly string $gender,
        public readonly string $forename,
        public readonly string $surname,
        public readonly string $dateOfBirth,
        public readonly string $uniqueId,
        public readonly Address $address,
        public readonly DateRange $employmentPeriod,
        public readonly string $workerGroupId,
        public readonly string $autoEnrolmentStatus,
        public readonly float $pensionableEarnings,
        public readonly float $employerContributionAmount,
        public readonly float $employeeContributionAmount,
        public readonly ?string $middleName = null,
        public readonly ?string $niNumber = null,
        public readonly ?string $homePhoneNumber = null,
        public readonly ?string $mobilePhoneNumber = null,
        public readonly ?string $personalEmailAddress = null,
        public readonly ?string $autoEnrolmentDate = null,
        public readonly ?string $schemeJoinDate = null,
        public readonly ?string $optOutDate = null,
        public readonly ?string $optInDate = null,
        public readonly ?string $partialContributionReason = null
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            gender: $data['gender'],
            forename: $data['forename'],
            surname: $data['surname'],
            dateOfBirth: $data['dateOfBirth'],
            uniqueId: $data['uniqueId'],
            address: Address::fromArray($data['address']),
            employmentPeriod: DateRange::fromArray($data['employmentPeriod']),
            workerGroupId: $data['workerGroupId'],
            autoEnrolmentStatus: $data['autoEnrolmentStatus'],
            pensionableEarnings: (float) $data['pensionableEarnings'],
            employerContributionAmount: (float) $data['employerContributionAmount'],
            employeeContributionAmount: (float) $data['employeeContributionAmount'],
            middleName: $data['middleName'] ?? null,
            niNumber: $data['niNumber'] ?? null,
            homePhoneNumber: $data['homePhoneNumber'] ?? null,
            mobilePhoneNumber: $data['mobilePhoneNumber'] ?? null,
            personalEmailAddress: $data['personalEmailAddress'] ?? null,
            autoEnrolmentDate: $data['autoEnrolmentDate'] ?? null,
            schemeJoinDate: $data['schemeJoinDate'] ?? null,
            optOutDate: $data['optOutDate'] ?? null,
            optInDate: $data['optInDate'] ?? null,
            partialContributionReason: $data['partialContributionReason'] ?? null
        );
    }

    /**
     * Convert to array for API request.
     */
    public function toArray(): array
    {
        $result = [
            'title' => $this->title,
            'gender' => $this->gender,
            'forename' => $this->forename,
            'surname' => $this->surname,
            'dateOfBirth' => $this->dateOfBirth,
            'uniqueId' => $this->uniqueId,
            'address' => $this->address->toArray(),
            'employmentPeriod' => $this->employmentPeriod->toArray(),
            'workerGroupId' => $this->workerGroupId,
            'autoEnrolmentStatus' => $this->autoEnrolmentStatus,
            'pensionableEarnings' => $this->pensionableEarnings,
            'employerContributionAmount' => $this->employerContributionAmount,
            'employeeContributionAmount' => $this->employeeContributionAmount,
        ];

        // Add optional fields only if they have values
        if ($this->middleName !== null) {
            $result['middleName'] = $this->middleName;
        }
        if ($this->niNumber !== null) {
            $result['niNumber'] = $this->niNumber;
        }
        if ($this->homePhoneNumber !== null) {
            $result['homePhoneNumber'] = $this->homePhoneNumber;
        }
        if ($this->mobilePhoneNumber !== null) {
            $result['mobilePhoneNumber'] = $this->mobilePhoneNumber;
        }
        if ($this->personalEmailAddress !== null) {
            $result['personalEmailAddress'] = $this->personalEmailAddress;
        }
        if ($this->autoEnrolmentDate !== null) {
            $result['autoEnrolmentDate'] = $this->autoEnrolmentDate;
        }
        if ($this->schemeJoinDate !== null) {
            $result['schemeJoinDate'] = $this->schemeJoinDate;
        }
        if ($this->optOutDate !== null) {
            $result['optOutDate'] = $this->optOutDate;
        }
        if ($this->optInDate !== null) {
            $result['optInDate'] = $this->optInDate;
        }
        if ($this->partialContributionReason !== null) {
            $result['partialContributionReason'] = $this->partialContributionReason;
        }

        return $result;
    }

    /**
     * Get total contribution amount.
     */
    public function getTotalContribution(): float
    {
        return $this->employerContributionAmount + $this->employeeContributionAmount;
    }
}
