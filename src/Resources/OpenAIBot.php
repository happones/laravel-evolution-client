<?php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class OpenAIBot
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
     * Create a new OpenAI bot.
     *
     * @param string $botName
     * @param string $model
     * @param array $options
     * @return array
     * @throws EvolutionApiException
     */
    public function create(string $botName, string $model, array $options = []): array
    {
        return $this->service->post('integrations/openai/bot', array_merge([
            'botName' => $botName,
            'model' => $model,
        ], $options));
    }

    /**
     * Find a specific OpenAI bot by its ID.
     *
     * @param string $botId
     * @return array
     * @throws EvolutionApiException
     */
    public function find(string $botId): array
    {
        return $this->service->get("integrations/openai/bot/$botId");
    }

    /**
     * Get all OpenAI bots.
     *
     * @return array
     * @throws EvolutionApiException
     */
    public function findAll(): array
    {
        return $this->service->get('integrations/openai/bot');
    }

    /**
     * Update an existing OpenAI bot.
     *
     * @param string $botId
     * @param array $data
     * @return array
     * @throws EvolutionApiException
     */
    public function update(string $botId, array $data): array
    {
        return $this->service->put("integrations/openai/bot/$botId", $data);
    }

    /**
     * Delete an OpenAI bot.
     *
     * @param string $botId
     * @return array
     * @throws EvolutionApiException
     */
    public function delete(string $botId): array
    {
        return $this->service->delete("integrations/openai/bot/$botId");
    }

    /**
     * Get OpenAI credentials.
     *
     * @return array
     * @throws EvolutionApiException
     */
    public function getCredentials(): array
    {
        return $this->service->get('integrations/openai/credentials');
    }

    /**
     * Set OpenAI credentials.
     *
     * @param string $apiKey
     * @return array
     * @throws EvolutionApiException
     */
    public function setCredentials(string $apiKey): array
    {
        return $this->service->post('integrations/openai/credentials', ['apiKey' => $apiKey]);
    }

    /**
     * Delete OpenAI credentials.
     *
     * @return array
     * @throws EvolutionApiException
     */
    public function deleteCredentials(): array
    {
        return $this->service->delete('integrations/openai/credentials');
    }

    /**
     * Update OpenAI settings.
     *
     * @param array $settings
     * @return array
     * @throws EvolutionApiException
     */
    public function updateSettings(array $settings): array
    {
        return $this->service->post('integrations/openai/settings', $settings);
    }

    /**
     * Get OpenAI settings.
     *
     * @return array
     * @throws EvolutionApiException
     */
    public function getSettings(): array
    {
        return $this->service->get('integrations/openai/settings');
    }

    /**
     * Change the status of the OpenAI integration.
     *
     * @param bool $isActive
     * @return array
     * @throws EvolutionApiException
     */
    public function changeStatus(bool $isActive): array
    {
        return $this->service->put('integrations/openai/status', ['isActive' => $isActive]);
    }

    /**
     * Find the session for a specific bot.
     *
     * @param string $botId
     * @return array
     * @throws EvolutionApiException
     */
    public function findSession(string $botId): array
    {
        return $this->service->get("integrations/openai/session/$botId");
    }
}