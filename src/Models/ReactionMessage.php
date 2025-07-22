<?php
// src/Models/ReactionMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class ReactionMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new ReactionMessage instance.
     *
     * @param array  $key
     * @param string $reaction
     */
    public function __construct(array $key, string $reaction)
    {
        $this->attributes = [
            'key'      => $key,
            'reaction' => $reaction,
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
