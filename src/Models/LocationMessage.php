<?php
// src/Models/LocationMessage.php

namespace Happones\LaravelEvolutionClient\Models;

class LocationMessage
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new LocationMessage instance.
     *
     * @param string             $number
     * @param string             $name
     * @param string             $address
     * @param float              $latitude
     * @param float              $longitude
     * @param int|null           $delay
     * @param QuotedMessage|null $quoted
     */
    public function __construct(
        string $number,
        string $name,
        string $address,
        float $latitude,
        float $longitude,
        ?int $delay = null,
        ?QuotedMessage $quoted = null
    ) {
        $this->attributes = [
            'number'    => $number,
            'name'      => $name,
            'address'   => $address,
            'latitude'  => $latitude,
            'longitude' => $longitude,
        ];

        if ($delay !== null) {
            $this->attributes['delay'] = $delay;
        }

        if ($quoted !== null) {
            $this->attributes['quoted'] = $quoted->toArray();
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
