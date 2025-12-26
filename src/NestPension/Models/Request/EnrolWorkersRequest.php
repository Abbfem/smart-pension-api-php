<?php

declare(strict_types=1);

namespace NestPension\Models\Request;

/**
 * Request model for enrolling workers into NEST pension scheme.
 */
class EnrolWorkersRequest
{
    private string $acknowledgementId;
    private array $workers = [];
    private array $groups = [];

    public function __construct(string $acknowledgementId = '')
    {
        $this->acknowledgementId = $acknowledgementId ?: 'ENR_' . date('YmdHis') . '_' . uniqid();
    }

    /**
     * Get acknowledgement ID.
     */
    public function getAcknowledgementId(): string
    {
        return $this->acknowledgementId;
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
     * Add worker to enrol.
     */
    public function addWorker(array $workerData): self
    {
        $this->workers[] = $workerData;
        return $this;
    }

    /**
     * Get all workers.
     */
    public function getWorkers(): array
    {
        return $this->workers;
    }

    /**
     * Set workers array.
     */
    public function setWorkers(array $workers): self
    {
        $this->workers = $workers;
        return $this;
    }

    /**
     * Add group configuration.
     */
    public function addGroup(array $groupData): self
    {
        $this->groups[] = $groupData;
        return $this;
    }

    /**
     * Get all groups.
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Set groups array.
     */
    public function setGroups(array $groups): self
    {
        $this->groups = $groups;
        return $this;
    }

    /**
     * Remove a worker by employee ID.
     */
    public function removeWorker(string $employeeId): self
    {
        $this->workers = array_filter($this->workers, function ($worker) use ($employeeId) {
            return ($worker['employee_id'] ?? '') !== $employeeId;
        });
        $this->workers = array_values($this->workers);
        return $this;
    }

    /**
     * Remove a group by group ID.
     */
    public function removeGroup(string $groupId): self
    {
        $this->groups = array_filter($this->groups, function ($group) use ($groupId) {
            return ($group['group_id'] ?? '') !== $groupId;
        });
        $this->groups = array_values($this->groups);
        return $this;
    }

    /**
     * Get worker count.
     */
    public function getWorkerCount(): int
    {
        return count($this->workers);
    }

    /**
     * Get group count.
     */
    public function getGroupCount(): int
    {
        return count($this->groups);
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
            'Workers' => [],
            'Groups' => []
        ];

        foreach ($this->workers as $worker) {
            $workerData = [
                'EmployeeId' => $worker['employee_id'] ?? '',
                'PersonDetails' => [
                    'Title' => $worker['title'] ?? '',
                    'FirstName' => $worker['first_name'] ?? '',
                    'LastName' => $worker['last_name'] ?? '',
                    'DateOfBirth' => $worker['date_of_birth'] ?? '',
                ],
                'NINumber' => $worker['ni_number'] ?? '',
                'EmploymentStartDate' => $worker['employment_start_date'] ?? '',
            ];

            if (isset($worker['salary'])) {
                $workerData['Salary'] = $worker['salary'];
            }

            if (isset($worker['email'])) {
                $workerData['Email'] = $worker['email'];
            }

            if (isset($worker['group_id'])) {
                $workerData['GroupId'] = $worker['group_id'];
            }

            if (isset($worker['joining_method'])) {
                $workerData['JoiningMethod'] = $worker['joining_method'];
            }

            $data['Workers'][] = ['Worker' => $workerData];
        }

        foreach ($this->groups as $group) {
            $groupData = [
                'GroupId' => $group['group_id'] ?? '',
                'GroupName' => $group['group_name'] ?? '',
            ];

            if (isset($group['employer_contribution_rate'])) {
                $groupData['EmployerContributionRate'] = $group['employer_contribution_rate'];
            }

            if (isset($group['member_contribution_rate'])) {
                $groupData['MemberContributionRate'] = $group['member_contribution_rate'];
            }

            $data['Groups'][] = ['Group' => $groupData];
        }

        return $data;
    }
}
