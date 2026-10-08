<?php

namespace SMART\Api\Auth;

/**
 * Supplies the bearer token sent with every API request.
 */
interface TokenProvider
{
    /**
     * Returns the current access token, or null when none is available.
     *
     * @return string|null
     */
    public function getToken(): ?string;

    /**
     * Forgets any cached token so the next getToken() call fetches a fresh one.
     * Providers that cannot refresh themselves should return false.
     *
     * @return bool true when a fresh token can be obtained on the next call
     */
    public function invalidate(): bool;
}
