<?php

namespace PeoplesPension\Request;

/**
 * HTTP Request methods.
 */
abstract class RequestMethod
{
    public const GET = 'GET';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const PATCH = 'PATCH';
    public const DELETE = 'DELETE';
}
