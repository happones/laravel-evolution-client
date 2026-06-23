<?php
// src/Resources/Instance.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Instance
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
     * Create a new Instance resource instance.
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
     * Get the current instance name.
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
     * @param string $instanceName
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function createInstance(string $instanceName): array
    {
        return $this->service->post("/instance/create", [
            'instanceName' => $instanceName,
        ]);
    }

    /**
     * Get the QR code for the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function getQrCode(): array
    {
        return $this->connect();
    }

    /**
     * Check if the instance is connected.
     *
     * @throws EvolutionApiException
     *
     * @return bool
     */
    public function isConnected(): bool
    {
        $status = $this->getStatus();

        return isset($status['status']) && $status['status'] === 'connected';
    }

    /**
     * Get the status of the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function getStatus(): array
    {
        return $this->service->get("/instance/status/{$this->instanceName}");
    }

    /**
     * Connect the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function connect(): array
    {
        return $this->service->get("/instance/connect/{$this->instanceName}");
    }

    /**
     * Disconnect the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function disconnect(): array
    {
        return $this->service->delete("/instance/logout/{$this->instanceName}");
    }

    /**
     * Delete the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function delete(): array
    {
        return $this->service->delete("/instance/delete/{$this->instanceName}");
    }

    /**
     * Restart the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function restart(): array
    {
        return $this->service->post("/instance/restart/{$this->instanceName}");
    }

    /**
     * Set the webhook URL for the instance.
     *
     * @param string $url
     * @param array  $events
     * @param bool   $enabled
     * @param array  $headers
     * @param bool   $base64
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function setWebhook(
        string $url,
        array $events = [],
        bool $enabled = true,
        array $headers = [],
        bool $base64 = false
    ): array {
        return $this->service->post("/webhook/set/{$this->instanceName}", [
            'enabled' => $enabled,
            'url'     => $url,
            'events'  => $events,
            'headers' => $headers,
            'base64'  => $base64,
        ]);
    }

    /**
     * Get the webhook configuration for the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function getWebhook(): array
    {
        return $this->service->get("/webhook/find/{$this->instanceName}");
    }

    /**
     * Get the connection state of the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function connectionState(): array
    {
        return $this->service->get("/instance/connectionState/{$this->instanceName}");
    }

    /**
     * Fetch all instances.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function fetchInstances(): array
    {
        return $this->service->get('/instance/fetchInstances');
    }
}
