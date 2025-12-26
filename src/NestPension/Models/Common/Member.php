<?php

declare(strict_types=1);

namespace NestPension\Models\Common;

/**
 * Member/worker information model for NEST pension API.
 * Based on NEST Member namespace (xmlns:mem).
 */
class Member extends Person
{
    private ?string $employeeId = null;
    private ?string $niNumber = null;
    private ?string $employmentStartDate = null;
    private ?string $employmentEndDate = null;
    private ?float $salary = null;
    private ?string $payrollFrequency = null;
    private ?string $nestMemberNumber = null;
    private ?string $groupId = null;
    private ?bool $isActiveEmployee = null;
    private ?string $joiningMethod = null;
    private ?string $employmentStatus = null;

    // Valid payroll frequencies
    public const VALID_PAYROLL_FREQUENCIES = ['Weekly', 'Fortnightly', 'Monthly', 'Annually'];

    // Valid employment statuses
    public const VALID_EMPLOYMENT_STATUSES = ['Active', 'Inactive', 'Leaver', 'Pensioner'];

    // Valid joining methods
    public const VALID_JOINING_METHODS = ['Auto-enrolment', 'Opt-in', 'Contract-enrolment'];

    /**
     * Set employee ID (required).
     */
    public function setEmployeeId(string $employeeId): self
    {
        $this->employeeId = $employeeId;
        return $this;
    }

    /**
     * Set National Insurance number (required).
     */
    public function setNiNumber(string $niNumber): self
    {
        $this->niNumber = strtoupper(str_replace(' ', '', $niNumber));
        return $this;
    }

    /**
     * Set employment start date.
     */
    public function setEmploymentStartDate(string $employmentStartDate): self
    {
        $this->employmentStartDate = $employmentStartDate;
        return $this;
    }

    /**
     * Set employment end date.
     */
    public function setEmploymentEndDate(?string $employmentEndDate): self
    {
        $this->employmentEndDate = $employmentEndDate;
        return $this;
    }

    /**
     * Set annual salary.
     */
    public function setSalary(?float $salary): self
    {
        $this->salary = $salary;
        return $this;
    }

    /**
     * Set payroll frequency.
     */
    public function setPayrollFrequency(?string $payrollFrequency): self
    {
        $this->payrollFrequency = $payrollFrequency;
        return $this;
    }

    /**
     * Set NEST member number.
     */
    public function setNestMemberNumber(?string $nestMemberNumber): self
    {
        $this->nestMemberNumber = $nestMemberNumber;
        return $this;
    }

    /**
     * Set group ID.
     */
    public function setGroupId(?string $groupId): self
    {
        $this->groupId = $groupId;
        return $this;
    }

    /**
     * Set active employee status.
     */
    public function setIsActiveEmployee(?bool $isActiveEmployee): self
    {
        $this->isActiveEmployee = $isActiveEmployee;
        return $this;
    }

    /**
     * Set joining method.
     */
    public function setJoiningMethod(?string $joiningMethod): self
    {
        $this->joiningMethod = $joiningMethod;
        return $this;
    }

    /**
     * Set employment status.
     */
    public function setEmploymentStatus(?string $employmentStatus): self
    {
        $this->employmentStatus = $employmentStatus;
        return $this;
    }

    // Getters

    public function getEmployeeId(): ?string
    {
        return $this->employeeId;
    }

    public function getNiNumber(): ?string
    {
        return $this->niNumber;
    }

    public function getEmploymentStartDate(): ?string
    {
        return $this->employmentStartDate;
    }

    public function getEmploymentEndDate(): ?string
    {
        return $this->employmentEndDate;
    }

    public function getSalary(): ?float
    {
        return $this->salary;
    }

    public function getPayrollFrequency(): ?string
    {
        return $this->payrollFrequency;
    }

    public function getNestMemberNumber(): ?string
    {
        return $this->nestMemberNumber;
    }

    public function getGroupId(): ?string
    {
        return $this->groupId;
    }

    public function getIsActiveEmployee(): ?bool
    {
        return $this->isActiveEmployee;
    }

    public function getJoiningMethod(): ?string
    {
        return $this->joiningMethod;
    }

    public function getEmploymentStatus(): ?string
    {
        return $this->employmentStatus;
    }

    /**
     * Convert to array for XML.
     */
    public function toArray(): array
    {
        $data = parent::toArray();

        $memberData = [
            'EmployeeId' => $this->employeeId,
            'NINumber' => $this->niNumber,
            'EmploymentStartDate' => $this->employmentStartDate,
            'EmploymentEndDate' => $this->employmentEndDate,
            'Salary' => $this->salary,
            'PayrollFrequency' => $this->payrollFrequency,
            'NestMemberNumber' => $this->nestMemberNumber,
            'GroupId' => $this->groupId,
            'JoiningMethod' => $this->joiningMethod,
            'EmploymentStatus' => $this->employmentStatus,
        ];

        if ($this->isActiveEmployee !== null) {
            $memberData['IsActiveEmployee'] = $this->isActiveEmployee ? 'true' : 'false';
        }

        return array_merge($data, array_filter($memberData, fn($value) => $value !== null));
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $member = new self();

        // Set person properties
        if (isset($data['Title'])) {
            $member->setTitle($data['Title']);
        }
        if (isset($data['FirstName'])) {
            $member->setFirstName($data['FirstName']);
        }
        if (isset($data['MiddleNames'])) {
            $member->setMiddleNames($data['MiddleNames']);
        }
        if (isset($data['LastName'])) {
            $member->setLastName($data['LastName']);
        }
        if (isset($data['DateOfBirth'])) {
            $member->setDateOfBirth($data['DateOfBirth']);
        }
        if (isset($data['Gender'])) {
            $member->setGender($data['Gender']);
        }
        if (isset($data['Email'])) {
            $member->setEmail($data['Email']);
        }
        if (isset($data['Phone'])) {
            $member->setPhone($data['Phone']);
        }
        if (isset($data['Address'])) {
            $member->setAddress(Address::fromArray($data['Address']));
        }

        // Set member-specific properties
        if (isset($data['EmployeeId'])) {
            $member->setEmployeeId($data['EmployeeId']);
        }
        if (isset($data['NINumber'])) {
            $member->setNiNumber($data['NINumber']);
        }
        if (isset($data['EmploymentStartDate'])) {
            $member->setEmploymentStartDate($data['EmploymentStartDate']);
        }
        if (isset($data['EmploymentEndDate'])) {
            $member->setEmploymentEndDate($data['EmploymentEndDate']);
        }
        if (isset($data['Salary'])) {
            $member->setSalary((float)$data['Salary']);
        }
        if (isset($data['PayrollFrequency'])) {
            $member->setPayrollFrequency($data['PayrollFrequency']);
        }
        if (isset($data['NestMemberNumber'])) {
            $member->setNestMemberNumber($data['NestMemberNumber']);
        }
        if (isset($data['GroupId'])) {
            $member->setGroupId($data['GroupId']);
        }
        if (isset($data['IsActiveEmployee'])) {
            $member->setIsActiveEmployee($data['IsActiveEmployee'] === 'true' || $data['IsActiveEmployee'] === true);
        }
        if (isset($data['JoiningMethod'])) {
            $member->setJoiningMethod($data['JoiningMethod']);
        }
        if (isset($data['EmploymentStatus'])) {
            $member->setEmploymentStatus($data['EmploymentStatus']);
        }

        return $member;
    }
}
