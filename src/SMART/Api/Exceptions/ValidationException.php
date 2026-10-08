<?php

namespace SMART\Api\Exceptions;

/**
 * HTTP 422 - the request failed validation; see getErrors() for the field level messages.
 */
class ValidationException extends ApiException
{
}
