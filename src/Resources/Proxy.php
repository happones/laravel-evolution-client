<?php
// src/Resources/Proxy.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Models\Proxy as ProxyModel;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Proxy
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
     * Create a new Proxy resource instance.
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
     * Set a proxy for the instance.
     *
     * @param bool        $enabled
     * @param string      $host
     * @param string      $port
     * @param string      $protocol
     * @param string|null $username
     * @param string|null $password
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function set(
        bool $enabled,
        string $host,
        string $port,
        string $protocol,
        ?string $username = null,
        ?string $password = null
    ): array {
        $proxy = new ProxyModel($enabled, $host, $port, $protocol, $username, $password);

        return $this->service->post("/proxy/set/{$this->instanceName}", $proxy->toArray());
    }

    /**
     * Find proxy settings for the instance.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function find(): array
    {
        return $this->service->get("/proxy/find/{$this->instanceName}");
    }
}
