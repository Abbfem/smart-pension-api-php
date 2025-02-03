<?php

namespace SMART\Contributions;

use SMART\Contributions\Request\GetRequest;

class ContributionsList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted contributions data.
     *
     * @param string $company_id Required. The ID of the company.
     * @param string $employee_id Required. The ID of the employee.
     * @param string $direction Sorting order of the results. Allowed values: 'ASC', 'DESC'.
     * @param array $filter Filtering criteria using deep object style with the following possible keys:
     *      - 'id' (array of int) List of contributions IDs.
     *      - 'forename' (array of string) List of forenames to filter by.
     *      - 'surname' (array of string) List of surnames to filter by.
     *      - 'external_id' (array of string) List of external reference IDs.
     *      - 'national_insurance_number' (array of string) List of National Insurance Numbers.
     *      - 'retirement_age' (array of int) List of retirement ages to filter by.
     *      - 'retirement_date' (array of string) List of retirement dates.
     *      - 'contributions_plan_participation' (array) Contributions plan participation filter.
     *          - 'state' (string) The state of participation.
     *          - 'updated_at' (array of string) List of last updated timestamps.
     *      - 'contributions_contribution_configurations' (array) Contributions contribution configurations filter.
     *          - 'updated_at' (string) Last updated timestamp.
     *      - 'opt_state' (array of string) List of opt-in states.
     *      - 'starts_on' (array of string) List of start dates.
     *      - 'ends_on' (array of string) List of end dates.
     *      - 'opt_out_on' (array of string) List of opt-out timestamps.
     *      - 'opt_in_on' (array of string) List of opt-in timestamps.
     *      - 'exit_reason' (array of string) List of exit reasons.
     *      - 'benefit_group_id' (array of int) List of benefit group IDs.
     *      - 'group_id' (array of int) List of group IDs.
     * @param array $include Specifies additional related data to include in the response.
     *      Allowed values: 'contributions', 'group', 'postponements', 
     *                      'contributions_plan_participation', 'contributions_contribution_configurations', 'employment_category'.
     * @param int $limit Maximum number of records to return.
     * @param int $offset Number of records to skip before starting to return results.
     * @param string $operator Logical operator for filtering criteria. Allowed values: 'or', 'and'.
     * @param string $sort The field by which the results should be sorted.
     *
     * @throws \SMART\Exceptions\InvalidDateFormatException
     */
    
   
    public function __construct(
        string $company_id,
        public string $employee_id,
        private string $direction = 'ASC',
        private array $filter = [],
        private array $include = [],
        private int $limit = 500,
        private int $offset = 0,
        private string $operator = 'and',
        private string $sort = ''
    )
    {
        parent::__construct($company_id);
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {

        return [
            // 'direction' => $this->direction,
            // 'filter'   => $this->filter,
            // 'include' => $this->include,
            'limit'   => $this->limit,
            // 'offset' => $this->offset,
            // 'operator'   => $this->operator,
            // 'sort'   => $this->sort,
        ];
    }

   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    protected function getSubApiPath(): string
    {
        return "/employees/{$this->employee_id}/contributions";
    }
}
