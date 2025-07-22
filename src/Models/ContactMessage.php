<?php
// src/Models/ContactMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class ContactMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new ContactMessage instance.
     *
     * @param string $number
     * @param array  $contacts
     */
    public function __construct(string $number, array $contacts)
    {
        $contactsArray = [];

        foreach ($contacts as $contact) {
            if ($contact instanceof Contact) {
                $contactsArray[] = $contact->toArray();
            } else {
                $contactsArray[] = $contact;
            }
        }

        $this->attributes = [
            'number'  => $number,
            'contact' => $contactsArray,
        ];
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
}
