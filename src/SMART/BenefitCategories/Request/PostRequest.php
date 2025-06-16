<?php
namespace SMART\BenefitCategories\Request;

use SMART\Request\PostBody;
use SMART\Response\Response;
use SMART\Request\RequestMethod;
use SMART\BenefitCategories\Request\BenefitCategoriesRequest;

abstract class PostRequest extends BenefitCategoriesRequest
{
    /** @var PostBody */
    protected $postBody;

    public function __construct($company_id, PostBody $postBody)
    {
        parent::__construct($company_id);

        $this->postBody = $postBody;
    }

    protected function getMethod(): string
    {
        return RequestMethod::POST;
    }

    public function fire()
    {
        $this->postBody->validate();

        return parent::fire();
    }

    protected function getHTTPClientOptions(): array
    {
        return array_merge([
            'json' => $this->postBody->toArray(),
        ], parent::getHTTPClientOptions());
    }
}
