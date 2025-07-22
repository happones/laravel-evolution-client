<?php
// src/Models/WebSocket.php

namespace Happones\LaravelEvolutionClient\Models;

class WebSocket
{
    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var array
     */
    protected $events;

    /**
     * Create a new WebSocket instance.
     *
     * @param bool  $enabled
     * @param array $events
     */
    public function __construct(bool $enabled, array $events = [])
    {
        $this->enabled = $enabled;
        $this->events  = $events;
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'enabled' => $this->enabled,
            'events'  => $this->events,
        ];
    }
}
