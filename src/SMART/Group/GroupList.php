<?php

namespace SMART\Group;

use SMART\Group\Request\GetRequest;

class GroupList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted employee data.
     *
     * @param string $direction Sorting order of the results. Allowed values: 'ASC', 'DESC'.
     * @param array $filter Filtering criteria using deep object style with the following possible keys:
     *      - 'id' (array of int) List of employee IDs.
     *      - 'forename' (array of string) List of forenames to filter by.
     *      - 'surname' (array of string) List of surnames to filter by.
     *      - 'external_id' (array of string) List of external reference IDs.
     *      - 'national_insurance_number' (array of string) List of National Insurance Numbers.
     *      - 'retirement_age' (array of int) List of retirement ages to filter by.
     *      - 'retirement_date' (array of string) List of retirement dates.
     *      - 'employee_plan_participation' (array) Employee plan participation filter.
     *          - 'state' (string) The state of participation.
     *          - 'updated_at' (array of string) List of last updated timestamps.
     *      - 'employee_contribution_configurations' (array) Employee contribution configurations filter.
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
     *                      'employee_plan_participation', 'employee_contribution_configurations', 'employment_category'.
     * @param int $limit Maximum number of records to return.
     * @param int $offset Number of records to skip before starting to return results.
     * @param string $operator Logical operator for filtering criteria. Allowed values: 'or', 'and'.
     * @param string $sort The field by which the results should be sorted.
     *
     * @throws \SMART\Exceptions\InvalidDateFormatException
     */
    
   
    public function __construct(
        string $company_id,
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
        return '/groups';
    }
}
