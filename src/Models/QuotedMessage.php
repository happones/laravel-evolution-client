<?php
// src/Models/QuotedMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class QuotedMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new QuotedMessage instance.
     *
     * @param array      $key
     * @param array|null $message
     */
    public function __construct(array $key, ?array $message = null)
    {
        $this->attributes = ['key' => $key];

        if ($message !== null) {
            $this->attributes['message'] = $message;
        }
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
