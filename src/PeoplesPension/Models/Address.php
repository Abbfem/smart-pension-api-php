<?php

namespace PeoplesPension\Models;

/**
 * Address model for employee addresses.
 */
class Address
{
    public function __construct(
        public readonly string $line1,
        public readonly ?string $line2 = null,
        public readonly ?string $line3 = null,
        public readonly ?string $line4 = null,
        public readonly ?string $line5 = null
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            line1: $data['line1'] ?? '',
            line2: $data['line2'] ?? null,
            line3: $data['line3'] ?? null,
            line4: $data['line4'] ?? null,
            line5: $data['line5'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $result = [
            'line1' => $this->line1,
        ];

        if ($this->line2 !== null) {
            $result['line2'] = $this->line2;
        }
        if ($this->line3 !== null) {
            $result['line3'] = $this->line3;
        }
        if ($this->line4 !== null) {
            $result['line4'] = $this->line4;
        }
        if ($this->line5 !== null) {
            $result['line5'] = $this->line5;
        }

        return $result;
    }
}
