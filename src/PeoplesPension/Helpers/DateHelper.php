<?php

namespace PeoplesPension\Helpers;

/**
 * Date helper functions for People's Pension API.
 */
class DateHelper
{
    /**
     * API date format.
     */
    public const FORMAT = 'Y-m-d';

    /**
     * Format a DateTime object to API date string.
     */
    public static function format(\DateTimeInterface $date): string
    {
        return $date->format(self::FORMAT);
    }

    /**
     * Parse an API date string to DateTime.
     */
    public static function parse(string $date): ?\DateTimeImmutable
    {
        $result = \DateTimeImmutable::createFromFormat(self::FORMAT, $date);
        return $result ?: null;
    }

    /**
     * Get today's date in API format.
     */
    public static function today(): string
    {
        return date(self::FORMAT);
    }

    /**
     * Check if a date is in the future.
     */
    public static function isFuture(string $date): bool
    {
        return $date > self::today();
    }

    /**
     * Check if a date is in the past.
     */
    public static function isPast(string $date): bool
    {
        return $date < self::today();
    }

    /**
     * Calculate age from date of birth.
     */
    public static function calculateAge(string $dateOfBirth): int
    {
        $dob = self::parse($dateOfBirth);
        if (!$dob) {
            return 0;
        }

        $now = new \DateTimeImmutable();
        return $now->diff($dob)->y;
    }

    /**
     * Get the first day of the current month.
     */
    public static function firstDayOfMonth(): string
    {
        return date('Y-m-01');
    }

    /**
     * Get the last day of the current month.
     */
    public static function lastDayOfMonth(): string
    {
        return date('Y-m-t');
    }

    /**
     * Get the first day of the current week (Monday).
     */
    public static function firstDayOfWeek(): string
    {
        return date(self::FORMAT, strtotime('monday this week'));
    }

    /**
     * Get the last day of the current week (Sunday).
     */
    public static function lastDayOfWeek(): string
    {
        return date(self::FORMAT, strtotime('sunday this week'));
    }
}
