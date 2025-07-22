<?php
// src/Models/Button.php

namespace Happones\LaravelEvolutionClient\Models;

class Button
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $displayText;

    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new Button instance.
     *
     * @param string $type
     * @param string $displayText
     * @param array  $attributes
     */
    public function __construct(string $type, string $displayText, array $attributes = [])
    {
        $this->type        = $type;
        $this->displayText = $displayText;
        $this->attributes  = $attributes;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $result = [
            'type'        => $this->type,
            'displayText' => $this->displayText,
        ];

        return array_merge($result, $this->attributes);
    }
}
