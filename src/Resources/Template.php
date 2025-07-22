<?php
// src/Resources/Template.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Template
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
     * Create a new Template resource instance.
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
     * Create a template.
     *
     * @param string      $name
     * @param string      $category
     * @param string      $language
     * @param array       $components
     * @param bool        $allowCategoryChange
     * @param string|null $webhookUrl
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function create(
        string $name,
        string $category,
        string $language,
        array $components,
        bool $allowCategoryChange = false,
        ?string $webhookUrl = null
    ): array {
        $data = [
            'name'                => $name,
            'category'            => $category,
            'allowCategoryChange' => $allowCategoryChange,
            'language'            => $language,
            'components'          => $components,
        ];

        if ($webhookUrl !== null) {
            $data['webhookUrl'] = $webhookUrl;
        }

        return $this->service->post("/template/create/{$this->instanceName}", $data);
    }

    /**
     * Find templates.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function find(): array
    {
        return $this->service->get("/template/find/{$this->instanceName}");
    }
}
