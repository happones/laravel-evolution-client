<?php
// src/Models/Contact.php

namespace Happones\LaravelEvolutionClient\Models;

class Contact
{
    /**
     * @var string
     */
    protected $fullName;

    /**
     * @var string
     */
    protected $wuid;

    /**
     * @var string
     */
    protected $phoneNumber;

    /**
     * @var string|null
     */
    protected $organization;

    /**
     * @var string|null
     */
    protected $email;

    /**
     * @var string|null
     */
    protected $url;

    /**
     * Create a new Contact instance.
     *
     * @param string      $fullName
     * @param string      $wuid
     * @param string      $phoneNumber
     * @param string|null $organization
     * @param string|null $email
     * @param string|null $url
     */
    public function __construct(
        string $fullName,
        string $wuid,
        string $phoneNumber,
        ?string $organization = null,
        ?string $email = null,
        ?string $url = null
    ) {
        $this->fullName     = $fullName;
        $this->wuid         = $wuid;
        $this->phoneNumber  = $phoneNumber;
        $this->organization = $organization;
        $this->email        = $email;
        $this->url          = $url;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $attributes = [
            'fullName'    => $this->fullName,
            'wuid'        => $this->wuid,
            'phoneNumber' => $this->phoneNumber,
        ];

        if ($this->organization !== null) {
            $attributes['organization'] = $this->organization;
        }

        if ($this->email !== null) {
            $attributes['email'] = $this->email;
        }

        if ($this->url !== null) {
            $attributes['url'] = $this->url;
        }

        return $attributes;
    }
}
