<?php

namespace App\Integrations\Klaviyo\Model;

class Profile {
    private $email;
    private $firstName;
    private $lastName;
    private $phoneNumber;

    public function __construct(
        string $email,
        string $firstName,
        string $lastName = '',
        string $phoneNumber = ''
    ) {
        $this->email = $email;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }
}