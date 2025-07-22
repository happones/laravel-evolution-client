<?php
// src/Resources/Label.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Models\Label as LabelModel;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Label
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
     * Create a new Label resource instance.
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
     * Find all labels.
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function findLabels(): array
    {
        return $this->service->get("/label/findLabels/{$this->instanceName}");
    }

    /**
     * Add a label to a chat.
     *
     * @param string $number
     * @param string $labelId
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function addLabel(string $number, string $labelId): array
    {
        return $this->handleLabel($number, $labelId, 'add');
    }

    /**
     * Add or remove a label to/from a chat.
     *
     * @param string $number
     * @param string $labelId
     * @param string $action
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function handleLabel(string $number, string $labelId, string $action): array
    {
        $label = new LabelModel($number, $labelId, $action);

        return $this->service->post("/label/handleLabel/{$this->instanceName}", $label->toArray());
    }

    /**
     * Remove a label from a chat.
     *
     * @param string $number
     * @param string $labelId
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function removeLabel(string $number, string $labelId): array
    {
        return $this->handleLabel($number, $labelId, 'remove');
    }
}
