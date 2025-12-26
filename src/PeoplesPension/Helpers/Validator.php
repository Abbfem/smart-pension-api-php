<?php

namespace PeoplesPension\Helpers;

/**
 * Validation helper functions for People's Pension API.
 */
class Validator
{
    /**
     * National Insurance Number pattern.
     */
    public const NI_NUMBER_PATTERN = '/^[A-Z]{2}[0-9]{6}[A-D]$/';

    /**
     * Date format expected by the API.
     */
    public const DATE_FORMAT = 'Y-m-d';

    /**
     * Validate National Insurance Number format.
     */
    public static function isValidNiNumber(?string $niNumber): bool
    {
        if ($niNumber === null) {
            return true; // NI number is optional
        }

        return preg_match(self::NI_NUMBER_PATTERN, strtoupper($niNumber)) === 1;
    }

    /**
     * Validate date format (YYYY-MM-DD).
     */
    public static function isValidDate(?string $date): bool
    {
        if ($date === null) {
            return true;
        }

        $d = \DateTime::createFromFormat(self::DATE_FORMAT, $date);
        return $d && $d->format(self::DATE_FORMAT) === $date;
    }

    /**
     * Validate phone number format.
     */
    public static function isValidPhoneNumber(?string $phone): bool
    {
        if ($phone === null || $phone === '') {
            return true;
        }

        return preg_match('/^[0-9 ]{0,20}$/', $phone) === 1;
    }

    /**
     * Validate email address format.
     */
    public static function isValidEmail(?string $email): bool
    {
        if ($email === null || $email === '') {
            return true;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false && strlen($email) <= 75;
    }

    /**
     * Validate currency amount (non-negative, max 2 decimal places).
     */
    public static function isValidAmount(float $amount): bool
    {
        if ($amount < 0) {
            return false;
        }

        // Check decimal places
        $parts = explode('.', (string) $amount);
        if (isset($parts[1]) && strlen($parts[1]) > 2) {
            return false;
        }

        return true;
    }

    /**
     * Format amount to 2 decimal places.
     */
    public static function formatAmount(float $amount): float
    {
        return round($amount, 2);
    }

    /**
     * Validate name field (forename, surname).
     */
    public static function isValidName(string $name, int $minLength = 1, int $maxLength = 30): bool
    {
        $length = strlen($name);
        
        if ($length < $minLength || $length > $maxLength) {
            return false;
        }

        // Check for numbers
        if (preg_match('/[0-9]/', $name)) {
            return false;
        }

        // Count apostrophes, hyphens, and spaces
        if (substr_count($name, "'") > 1) {
            return false;
        }
        if (substr_count($name, "-") > 4) {
            return false;
        }
        if (substr_count($name, " ") > 4) {
            return false;
        }

        return true;
    }

    /**
     * Validate address line length.
     */
    public static function isValidAddressLine(?string $line, int $maxLength = 50): bool
    {
        if ($line === null || $line === '') {
            return true;
        }

        return strlen($line) <= $maxLength;
    }

    /**
     * Validate unique ID.
     */
    public static function isValidUniqueId(string $uniqueId): bool
    {
        $length = strlen($uniqueId);
        return $length >= 1 && $length <= 50;
    }

    /**
     * Validate worker group ID.
     */
    public static function isValidWorkerGroupId(string $workerGroupId): bool
    {
        return strlen($workerGroupId) <= 40;
    }
}
