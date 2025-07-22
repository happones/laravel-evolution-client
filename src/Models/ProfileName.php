<?php

namespace Happones\LaravelEvolutionClient\Models;

class ProfileName extends Profile
{
    /**
     * Create a new ProfileName instance.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct(['name' => $name]);
    }
}
