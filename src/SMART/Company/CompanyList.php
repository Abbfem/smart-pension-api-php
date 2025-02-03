<?php

namespace SMART\Company;

use SMART\Company\Request\GetRequest;



class CompanyList extends GetRequest
{
   

    /**
     * Fetches filtered and sorted data based on the given parameters.
     *
     * @param string $direction Sorting order of the results. Allowed values: 'ASC', 'DESC'.
     * @param array $filter Filtering criteria with the following possible keys:
     *      - 'id' (array of int) List of IDs to filter by.
     *      - 'name' (array of string) List of names to filter by.
     *      - 'state' (array of string) List of states to filter by.
     *      - 'scheme_detail' (array) Scheme details filter.
     *      - 'adviser_companies' (array) Adviser companies filter.
     *      - 'adviser_id' (int) Filter by adviser ID.
     *      - 'company_id' (int) Filter by company ID.
     * @param array $include Specifies additional related data to include in the response.
     *      Allowed values: 'company_automation', 'scheme_detail', 'adviser_companies', 'customers'.
     * @param int $limit Maximum number of records to return.
     * @param int $offset Number of records to skip before starting to return results.
     * @param string $operator Logical operator for filtering criteria. Allowed values: 'or', 'and'.
     * @param string $sort The field by which the results should be sorted.
     *
     * @throws \SMART\Exceptions\InvalidDateFormatException
     */

   
    public function __construct(
        private string $direction = 'ASC',
        private array $filter = [],
        private array $include = [],
        private int $limit = 10,
        private int $offset = 0,
        private string $operator = 'and',
        private string $sort = ''
    )
    {
       
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {
        return [
            'direction' => $this->direction,
            'filter'   => $this->filter,
            'include' => $this->include,
            'limit'   => $this->limit,
            'offset' => $this->offset,
            'operator'   => $this->operator,
            'sort'   => $this->sort,
        ];
    }

   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    protected function getCompanyApiPath(): string
    {
        return '';
    }
}
