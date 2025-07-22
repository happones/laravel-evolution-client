<?php
// src/Models/TextMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class TextMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new TextMessage instance.
     *
     * @param string             $number
     * @param string             $text
     * @param int|null           $delay
     * @param QuotedMessage|null $quoted
     * @param bool|null          $linkPreview
     * @param bool|null          $mentionsEveryOne
     * @param array|null         $mentioned
     */
    public function __construct(
        string $number,
        string $text,
        ?int $delay = null,
        ?QuotedMessage $quoted = null,
        ?bool $linkPreview = null,
        ?bool $mentionsEveryOne = null,
        ?array $mentioned = null
    ) {
        $this->attributes = [
            'number' => $number,
            'text'   => $text,
        ];

        if ($delay !== null) {
            $this->attributes['delay'] = $delay;
        }

        if ($quoted !== null) {
            $this->attributes['quoted'] = $quoted->toArray();
        }

        if ($linkPreview !== null) {
            $this->attributes['linkPreview'] = $linkPreview;
        }

        if ($mentionsEveryOne !== null) {
            $this->attributes['mentionsEveryOne'] = $mentionsEveryOne;
        }

        if ($mentioned !== null) {
            $this->attributes['mentioned'] = $mentioned;
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
