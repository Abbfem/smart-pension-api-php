<?php

namespace PeoplesPension\Request;

/**
 * Request header values.
 */
abstract class RequestHeaderValue
{
    /** JSON API Content Type as used by People's Pension API */
    public const APPLICATION_VND_API_JSON = 'application/vnd.api+json';
    
    public const APPLICATION_JSON = 'application/json';
}
