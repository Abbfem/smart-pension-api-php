<?php

namespace SMART\Api\Exceptions;

/**
 * HTTP 429 - the rate limit was hit (500 req/min by default, lower on some endpoints).
 */
class RateLimitException extends ApiException
{
    /**
     * Seconds to wait before retrying, when the API sends a Retry-After header.
     */
    public function getRetryAfter(): ?int
    {
        $value = $this->getResponse()->getHeaderLine('Retry-After');

        return is_numeric($value) ? (int) $value : null;
    }
}
