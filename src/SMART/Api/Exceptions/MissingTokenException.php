<?php

namespace SMART\Api\Exceptions;

use SMART\Exceptions\SMARTException;

/**
 * Thrown before sending a request that needs a bearer token when no token is available.
 */
class MissingTokenException extends SMARTException
{
}
