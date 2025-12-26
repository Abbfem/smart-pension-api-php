<?php

declare(strict_types=1);

namespace NestPension\Models\Common;

/**
 * Address model for NEST pension API.
 * Based on NEST Address namespace (xmlns:add).
 */
class Address
{
    private ?string $addressLine1 = null;
    private ?string $addressLine2 = null;
    private ?string $addressLine3 = null;
    private ?string $addressLine4 = null;
    private ?string $town = null;
    private ?string $county = null;
    private ?string $postCode = null;
    private ?string $country = null;

    /**
     * Set address line 1.
     */
    public function setAddressLine1(string $addressLine1): self
    {
        $this->addressLine1 = $addressLine1;
        return $this;
    }

    /**
     * Set address line 2.
     */
    public function setAddressLine2(?string $addressLine2): self
    {
        $this->addressLine2 = $addressLine2;
        return $this;
    }

    /**
     * Set address line 3.
     */
    public function setAddressLine3(?string $addressLine3): self
    {
        $this->addressLine3 = $addressLine3;
        return $this;
    }

    /**
     * Set address line 4.
     */
    public function setAddressLine4(?string $addressLine4): self
    {
        $this->addressLine4 = $addressLine4;
        return $this;
    }

    /**
     * Set town.
     */
    public function setTown(?string $town): self
    {
        $this->town = $town;
        return $this;
    }

    /**
     * Set county.
     */
    public function setCounty(?string $county): self
    {
        $this->county = $county;
        return $this;
    }

    /**
     * Set post code.
     */
    public function setPostCode(?string $postCode): self
    {
        $this->postCode = $postCode;
        return $this;
    }

    /**
     * Set country.
     */
    public function setCountry(?string $country): self
    {
        $this->country = $country;
        return $this;
    }

    // Getters

    public function getAddressLine1(): ?string
    {
        return $this->addressLine1;
    }

    public function getAddressLine2(): ?string
    {
        return $this->addressLine2;
    }

    public function getAddressLine3(): ?string
    {
        return $this->addressLine3;
    }

    public function getAddressLine4(): ?string
    {
        return $this->addressLine4;
    }

    public function getTown(): ?string
    {
        return $this->town;
    }

    public function getCounty(): ?string
    {
        return $this->county;
    }

    public function getPostCode(): ?string
    {
        return $this->postCode;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * Get formatted address.
     */
    public function getFormattedAddress(): string
    {
        $parts = array_filter([
            $this->addressLine1,
            $this->addressLine2,
            $this->addressLine3,
            $this->addressLine4,
            $this->town,
            $this->county,
            $this->postCode,
            $this->country
        ]);

        return implode(', ', $parts);
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        return array_filter([
            'AddressLine1' => $this->addressLine1,
            'AddressLine2' => $this->addressLine2,
            'AddressLine3' => $this->addressLine3,
            'AddressLine4' => $this->addressLine4,
            'Town' => $this->town,
            'County' => $this->county,
            'PostCode' => $this->postCode,
            'Country' => $this->country,
        ], fn($value) => $value !== null);
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $address = new self();

        if (isset($data['AddressLine1'])) {
            $address->setAddressLine1($data['AddressLine1']);
        }
        if (isset($data['AddressLine2'])) {
            $address->setAddressLine2($data['AddressLine2']);
        }
        if (isset($data['AddressLine3'])) {
            $address->setAddressLine3($data['AddressLine3']);
        }
        if (isset($data['AddressLine4'])) {
            $address->setAddressLine4($data['AddressLine4']);
        }
        if (isset($data['Town'])) {
            $address->setTown($data['Town']);
        }
        if (isset($data['County'])) {
            $address->setCounty($data['County']);
        }
        if (isset($data['PostCode'])) {
            $address->setPostCode($data['PostCode']);
        }
        if (isset($data['Country'])) {
            $address->setCountry($data['Country']);
        }

        return $address;
    }
}
