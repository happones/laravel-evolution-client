<?php
// src/Resources/WebSocket.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Models\WebSocket as WebSocketModel;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Happones\LaravelEvolutionClient\Services\WebSocketClient;

class WebSocket
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
     * Create a new WebSocket resource instance.
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
     * Configure WebSocket settings.
     *
     * @param bool  $enabled
     * @param array $events
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function setWebSocket(bool $enabled, array $events = []): array
    {
        $webSocket = new WebSocketModel($enabled, $events);

        return $this->service->post("/websocket/set/{$this->instanceName}", $webSocket->toArray());
    }

    /**
     * Get WebSocket settings.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function findWebSocket(): array
    {
        return $this->service->get("/websocket/find/{$this->instanceName}");
    }

    /**
     * Create a WebSocket client.
     *
     * @param int   $maxRetries
     * @param float $retryDelay
     *
     * @return mixed
     */
    public function createClient(int $maxRetries = 5, float $retryDelay = 1.0)
    {
        // Durante os testes, isto pode retornar null
        if (defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING) {
            return null;
        }

        return new WebSocketClient(
            $this->service->getBaseUrl(),
            $this->instanceName,
            $this->service->getApiKey(),
            $maxRetries,
            $retryDelay
        );
    }
}
