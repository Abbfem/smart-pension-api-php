<?php

namespace PeoplesPension\Request;

/**
 * People's Pension API URL constants.
 */
abstract class RequestURL
{
    /** Sandbox API base URL */
    public const SANDBOX = 'https://api-sandbox.peoplespartnership.co.uk';
    
    /** Live API base URL */
    public const LIVE = 'https://api.peoplespartnership.co.uk';
    
    /** API Version path */
    public const API_PATH = '/api/v2';
}
