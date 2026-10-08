<?php

namespace SMART\Api;

/**
 * Builds Keystone query strings.
 *
 * Keystone (Rails) expects bracket notation without numeric indexes:
 *   ['include' => ['owner', 'groups']]          -> include[]=owner&include[]=groups
 *   ['filter' => ['state' => 'paid']]           -> filter[state]=paid
 *   ['filter' => ['name' => ['a', 'b']]]        -> filter[name][]=a&filter[name][]=b
 *   ['fields' => ['employees' => ['id']]]       -> fields[employees][]=id
 *   ['company_match' => true]                   -> company_match=true
 */
final class Query
{
    public static function build(array $query): string
    {
        $pairs = [];

        foreach ($query as $key => $value) {
            self::append($pairs, (string) $key, $value);
        }

        return implode('&', $pairs);
    }

    private static function append(array &$pairs, string $key, $value): void
    {
        if ($value === null) {
            return;
        }

        if (is_array($value)) {
            $isList = $value === [] || array_keys($value) === range(0, count($value) - 1);

            foreach ($value as $subKey => $subValue) {
                self::append($pairs, $isList ? "{$key}[]" : "{$key}[{$subKey}]", $subValue);
            }

            return;
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif ($value instanceof \DateTimeInterface) {
            $value = $value->format('H:i:s') === '00:00:00' ? $value->format('Y-m-d') : $value->format(\DATE_ATOM);
        }

        $pairs[] = self::encodeKey($key).'='.rawurlencode((string) $value);
    }

    private static function encodeKey(string $key): string
    {
        // keep the brackets readable, encode everything else
        return str_replace(['%5B', '%5D'], ['[', ']'], rawurlencode($key));
    }
}
