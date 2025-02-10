<?php

namespace SMART\Adviser;

use SMART\Adviser\Request\GetRequest;


class GetAdviser extends GetRequest
{
    /** @var string */
    private $adviser_id;

    public function __construct(string $adviser_id)
    {
        
        $this->adviser_id = $adviser_id;
    }

    /**
     * @return array
     */
    protected function getQueryString(): array
    {
        return [];
    }

    protected function getAdviserApiPath(): string
    {
        return "/{$this->adviser_id}";
    }

    
}
