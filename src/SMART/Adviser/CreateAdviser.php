<?php

namespace SMART\Adviser;

use SMART\Adviser\Request\NewPostBody;
use SMART\Adviser\Request\PostRequest;


class CreateAdviser extends PostRequest
{
    /**
     * Create new adviser and scheme details.
     *
     * @param NewPostBody $postBody The adviser and scheme details in JSON format with the following fields:
     *  -string $logo The company logo URL or base64-encoded string.
     *  -string $name Required. The company name.
     *  -string $address Required. The company address.
     *  -string|null $signup_start The start date for signup.
     *  -string|null $signup_finish The end date for signup.
     *  -string|null $channel The channel through which the company was registered.
     *  -string|null $agent_number The agent number associated with the company.
     *  -string $email Required. The company's email address.
     *  -string $telephone Required. The company's telephone number.
     *  -bool|null $annuity_option_enabled Whether annuity options are enabled.
     *  -bool|null $marketing_included Whether marketing is included.
     *  -string $password Required. The account password.
     *  -string|null $title The title of the company representative (e.g., Mr, Mrs, Dr).
     *  -string $forename Required. The forename of the company representative.
     *  -string $surname Required. The surname of the company representative.
     *
     */
    public function __construct(NewPostBody $postBody)
    {
        parent::__construct($postBody);
    }

    protected function getAdviserApiPath(): string
    {
        return '';
    }

    
}
