<?php

namespace SMART\Request;

abstract class RequestURL
{
    /** @var string URL of sandbox environment */
    public const AUTH_SANDBOX = 'https://id.sandbox.autoenrolment.co.uk';

    public const SANDBOX = 'https://api.sandbox.autoenrolment.co.uk';

    /** @var string URL of live environment */
    public const AUTH_LIVE = 'https://id.autoenrolment.co.uk';

    public const LIVE = 'https://api.autoenrolment.co.uk';
}
