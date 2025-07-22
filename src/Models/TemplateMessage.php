<?php
// src/Models/TemplateMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class TemplateMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new TemplateMessage instance.
     *
     * @param string      $number
     * @param string      $name
     * @param string      $language
     * @param array       $components
     * @param string|null $webhookUrl
     */
    public function __construct(
        string $number,
        string $name,
        string $language,
        array $components,
        ?string $webhookUrl = null
    ) {
        $this->attributes = [
            'number'     => $number,
            'name'       => $name,
            'language'   => $language,
            'components' => $components,
        ];

        if ($webhookUrl !== null) {
            $this->attributes['webhookUrl'] = $webhookUrl;
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
