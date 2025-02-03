<?php

namespace SMART\Contribution;

use SMART\Contribution\Request\GetRequest;

class ContributionList extends GetRequest
{
   

    /**
     * Retrieves filtered and sorted contributions data.
     *
     * @param string $company_id company id to fetch it's contribution list.
     *
     */
    
   
    public function __construct(
        string $company_id,
    )
    {
        parent::__construct($company_id);
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {

        return [];
    }

   

    /**
     * Get VAT Api path, the path should be after {$this->vrn}.
     *
     * @return string
     */
    protected function getSubApiPath(): string
    {
        return '/contributions';
    }
}
