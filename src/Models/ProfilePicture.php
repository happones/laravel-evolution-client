<?php

namespace Happones\LaravelEvolutionClient\Models;

class ProfilePicture extends Profile
{
    /**
     * Create a new ProfilePicture instance.
     *
     * @param string $picture
     */
    public function __construct(string $picture)
    {
        parent::__construct(['picture' => $picture]);
    }
}
