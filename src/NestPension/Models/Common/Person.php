<?php

declare(strict_types=1);

namespace NestPension\Models\Common;

/**
 * Person information model for NEST pension API.
 * Based on NEST Person namespace (xmlns:per).
 */
class Person
{
    protected ?string $title = null;
    protected ?string $firstName = null;
    protected ?string $middleNames = null;
    protected ?string $lastName = null;
    protected ?string $dateOfBirth = null;
    protected ?string $gender = null;
    protected ?string $email = null;
    protected ?string $phone = null;
    protected ?Address $address = null;

    // Valid titles
    public const VALID_TITLES = ['Mr', 'Mrs', 'Miss', 'Ms', 'Dr', 'Prof', 'Rev'];

    // Valid genders
    public const VALID_GENDERS = ['Male', 'Female', 'Unspecified'];

    /**
     * Set title.
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Set first name.
     */
    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * Set middle names.
     */
    public function setMiddleNames(?string $middleNames): self
    {
        $this->middleNames = $middleNames;
        return $this;
    }

    /**
     * Set last name.
     */
    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * Set date of birth.
     */
    public function setDateOfBirth(string $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;
        return $this;
    }

    /**
     * Set gender.
     */
    public function setGender(?string $gender): self
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Set email.
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Set phone.
     */
    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Set address.
     */
    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    /**
     * Get title.
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * Get first name.
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * Get middle names.
     */
    public function getMiddleNames(): ?string
    {
        return $this->middleNames;
    }

    /**
     * Get last name.
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * Get date of birth.
     */
    public function getDateOfBirth(): ?string
    {
        return $this->dateOfBirth;
    }

    /**
     * Get gender.
     */
    public function getGender(): ?string
    {
        return $this->gender;
    }

    /**
     * Get email.
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Get phone.
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Get address.
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * Get full name.
     */
    public function getFullName(): string
    {
        $parts = array_filter([
            $this->title,
            $this->firstName,
            $this->middleNames,
            $this->lastName
        ]);

        return implode(' ', $parts);
    }

    /**
     * Convert to array.
     */
    public function toArray(): array
    {
        $data = [
            'Title' => $this->title,
            'FirstName' => $this->firstName,
            'MiddleNames' => $this->middleNames,
            'LastName' => $this->lastName,
            'DateOfBirth' => $this->dateOfBirth,
            'Gender' => $this->gender,
            'Email' => $this->email,
            'Phone' => $this->phone,
        ];

        if ($this->address) {
            $data['Address'] = $this->address->toArray();
        }

        return array_filter($data, fn($value) => $value !== null);
    }

    /**
     * Create from array.
     */
    public static function fromArray(array $data): self
    {
        $person = new self();

        if (isset($data['Title'])) {
            $person->setTitle($data['Title']);
        }
        if (isset($data['FirstName'])) {
            $person->setFirstName($data['FirstName']);
        }
        if (isset($data['MiddleNames'])) {
            $person->setMiddleNames($data['MiddleNames']);
        }
        if (isset($data['LastName'])) {
            $person->setLastName($data['LastName']);
        }
        if (isset($data['DateOfBirth'])) {
            $person->setDateOfBirth($data['DateOfBirth']);
        }
        if (isset($data['Gender'])) {
            $person->setGender($data['Gender']);
        }
        if (isset($data['Email'])) {
            $person->setEmail($data['Email']);
        }
        if (isset($data['Phone'])) {
            $person->setPhone($data['Phone']);
        }
        if (isset($data['Address'])) {
            $person->setAddress(Address::fromArray($data['Address']));
        }

        return $person;
    }
}
