<?php

namespace SMART\Adviser;

use SMART\Adviser\Request\NewPostBody;
use SMART\Adviser\Request\PatchRequest;



class PatchAdviser extends PatchRequest
{
    /** @var string */
    private $adviser_id;

    /**
     * Generates an array with adviser details.
     *
     * @param string      $adviser_id           The adviser ID.
     * @param NewPostBody $postBody             The adviser details.
     *  -string      $logo                 URL or file path to the company logo.
     *  -string      $name                 Name of the company.
     *  -string      $address              Company address.
     *  -string|null $signupStart          Signup start date (YYYY-MM-DD) (optional).
     *  -string|null $signupFinish         Signup finish date (YYYY-MM-DD) (optional).
     *  -string|null $channel              Channel type (optional).
     *  -string|null $agentNumber          Agent number (optional).
     *  -string      $email                Company email.
     *  -string      $telephone            Company contact number.
     *  -bool        $annuityOptionEnabled Whether the annuity option is enabled.
     *
     * @return array The structured company data.
     */
    public function __construct(string $adviser_id, NewPostBody $postBody)
    {
        parent::__construct($postBody);
        $this->adviser_id = $adviser_id;
    }

    protected function getAdviserApiPath(): string
    {
        return "/{$this->adviser_id}";
    }

    
}
