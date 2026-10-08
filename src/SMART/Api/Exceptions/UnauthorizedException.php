<?php

namespace SMART\Api\Exceptions;

/**
 * HTTP 401 - the token is missing, expired or lacks the scope for this endpoint.
 */
class UnauthorizedException extends ApiException
{
}
