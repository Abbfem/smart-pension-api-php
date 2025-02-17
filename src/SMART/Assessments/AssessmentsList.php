<?php

namespace SMART\Assessments;

use SMART\Assessments\Request\GetRequest;

class AssessmentsList extends GetRequest
{
   

    /**

        * Retrieves filtered and sorted assessments data.
        *
        * @param string $company_id Required. The ID of the company.
        * @param string $employee_id Required. The ID of the employee.
        * @param string $direction Sorting order of the results. Allowed values: 'ASC', 'DESC'.
        * @param array $filter Filtering criteria using deep object style with the following possible keys:
        *      - 'category' (array of string) List of contribution categories.
        *      - 'period_type' (array of string) List of period types.
        *      - 'occurred_on' (array of string) List of contribution occurrence dates.
        *      - 'cancelled_at' (array of string) List of cancellation dates.
        * @param int $limit Maximum number of records to return.
        * @param int $offset Number of records to skip before returning results.
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
        return "/employees/{$this->employee_id}/assessments";
    }
}
