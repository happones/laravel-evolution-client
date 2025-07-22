<?php
// src/Models/Call.php

namespace Happones\LaravelEvolutionClient\Models;

class Call
{
    /**
     * @var string
     */
    protected $number;

    /**
     * @var bool
     */
    protected $isVideo;

    /**
     * @var int
     */
    protected $callDuration;

    /**
     * Create a new Call instance.
     *
     * @param string $number       The phone number
     * @param bool   $isVideo      Whether it's a video call
     * @param int    $callDuration The call duration in seconds
     */
    public function __construct(string $number, bool $isVideo, int $callDuration)
    {
        $this->number       = $number;
        $this->isVideo      = $isVideo;
        $this->callDuration = $callDuration;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'number'       => $this->number,
            'isVideo'      => $this->isVideo,
            'callDuration' => $this->callDuration,
        ];
    }
}
