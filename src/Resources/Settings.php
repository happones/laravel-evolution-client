<?php
// src/Resources/Settings.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Models\Settings as SettingsModel;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Settings
{
    /**
     * @var EvolutionService The Evolution service
     */
    protected EvolutionService $service;

    /**
     * @var string The instance name
     */
    protected string $instanceName;

    /**
     * Create a new Settings resource instance.
     *
     * @param EvolutionService $service
     * @param string           $instanceName
     */
    public function __construct(EvolutionService $service, string $instanceName)
    {
        $this->service      = $service;
        $this->instanceName = $instanceName;
    }

    /**
     * Get the instance name.
     *
     * @return string
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Set the instance name.
     *
     * @param string $instanceName
     *
     * @return void
     */
    public function setInstanceName(string $instanceName): void
    {
        $this->instanceName = $instanceName;
    }

    /**
     * Set instance settings.
     *
     * @param bool        $rejectCall
     * @param string|null $msgCall
     * @param bool        $groupsIgnore
     * @param bool        $alwaysOnline
     * @param bool        $readMessages
     * @param bool        $syncFullHistory
     * @param bool        $readStatus
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function set(
        bool $rejectCall = false,
        ?string $msgCall = null,
        bool $groupsIgnore = false,
        bool $alwaysOnline = false,
        bool $readMessages = false,
        bool $syncFullHistory = false,
        bool $readStatus = false
    ): array {
        $settings = new SettingsModel(
            $rejectCall,
            $msgCall,
            $groupsIgnore,
            $alwaysOnline,
            $readMessages,
            $syncFullHistory,
            $readStatus
        );

        return $this->service->post("/settings/set/{$this->instanceName}", $settings->toArray());
    }

    /**
     * Find instance settings.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function find(): array
    {
        return $this->service->get("/settings/find/{$this->instanceName}");
    }
}
