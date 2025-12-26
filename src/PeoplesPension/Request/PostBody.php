<?php

namespace PeoplesPension\Request;

use PeoplesPension\Exceptions\InvalidPostBodyException;

/**
 * Interface for POST request body.
 */
interface PostBody
{
    /**
     * Validate the post body, it should throw an Exception if something is wrong.
     *
     * @throws InvalidPostBodyException
     */
    public function validate(): void;

    /**
     * Return post body as an array to be used in the request.
     *
     * @return array
     */
    public function toArray(): array;
}
