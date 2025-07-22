<?php
// src/Models/Settings.php

namespace Happones\LaravelEvolutionClient\Models;

class Settings
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * Create a new Settings instance.
     *
     * @param bool        $rejectCall
     * @param string|null $msgCall
     * @param bool        $groupsIgnore
     * @param bool        $alwaysOnline
     * @param bool        $readMessages
     * @param bool        $syncFullHistory
     * @param bool        $readStatus
     */
    public function __construct(
        bool $rejectCall = false,
        ?string $msgCall = null,
        bool $groupsIgnore = false,
        bool $alwaysOnline = false,
        bool $readMessages = false,
        bool $syncFullHistory = false,
        bool $readStatus = false
    ) {
        $this->attributes = [
            'rejectCall'      => $rejectCall,
            'groupsIgnore'    => $groupsIgnore,
            'alwaysOnline'    => $alwaysOnline,
            'readMessages'    => $readMessages,
            'syncFullHistory' => $syncFullHistory,
            'readStatus'      => $readStatus,
        ];

        if ($msgCall !== null) {
            $this->attributes['msgCall'] = $msgCall;
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
