<?php
// src/Models/Message.php

namespace Happones\LaravelEvolutionClient\Models;

class Message
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new Message instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
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
