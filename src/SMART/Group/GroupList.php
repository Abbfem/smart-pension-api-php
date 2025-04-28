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
     
     *      - 'name' (array of string) List of names to filter by.
     *      - 'include_in_payment_schedule' (boolean) List of payment schedule to filter by.
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
