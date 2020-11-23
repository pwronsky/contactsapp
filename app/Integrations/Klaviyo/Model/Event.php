<?php

namespace App\Integrations\Klaviyo\Model;

class Event {
    private $name;
    private $customerProperties;
    private $properties;
    private $time;

    public function __construct(string $name, array $customerProperties = [], array $properties = [], $time = '')
    {
        $this->name = $name;
        $this->customerProperties = $customerProperties;
        $this->properties = $properties;
        $this->time = $this ?: time();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getCustomerProperties(): array
    {
        return $this->customerProperties;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @return Event|int
     */
    public function getTime()
    {
        return $this->time;
    }
}