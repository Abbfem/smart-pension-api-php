<?php

namespace PeoplesPension\Models;

/**
 * Date Range model.
 * 
 * Used for pay reference periods, effective dates, and employment periods.
 */
class DateRange
{
    public function __construct(
        public readonly ?string $start = null,
        public readonly ?string $end = null
    ) {}

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            start: $data['start'] ?? null,
            end: $data['end'] ?? null
        );
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $result = [];
        
        if ($this->start !== null) {
            $result['start'] = $this->start;
        }
        
        if ($this->end !== null) {
            $result['end'] = $this->end;
        }
        
        return $result;
    }
}
