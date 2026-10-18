<?php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;

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
     */
    public function __construct(EvolutionService $service, string $instanceName)
    {
        $this->service = $service;
        $this->instanceName = $instanceName;
    }

    /**
     * Get the instance name.
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Set the instance name.
     */
    public function setInstanceName(string $instanceName): void
    {
        $this->instanceName = $instanceName;
    }

    /**
     * Create a new OpenAI bot.
     *
     * @param string $openaiCredsId Id of the stored OpenAI credentials (see setCredentials())
     * @param string $model OpenAI model, e.g. gpt-4o
     * @param string $botType Bot type: chatCompletion|assistant
     * @param bool $enabled Whether the bot is enabled
     * @param array<string, mixed> $options Extra fields (triggerType, systemMessages, assistantId, ...)
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function create(
        string $openaiCredsId,
        string $model,
        string $botType = 'chatCompletion',
        bool $enabled = true,
        array $options = []
    ): array {
        if (! in_array($botType, ['chatCompletion', 'assistant'], true)) {
            throw new InvalidArgumentException('Bot type must be one of: chatCompletion, assistant');
        }

        return $this->service->post("/openai/create/{$this->instanceName}", array_merge([
            'enabled' => $enabled,
            'openaiCredsId' => $openaiCredsId,
            'botType' => $botType,
            'model' => $model,
        ], $options));
    }

    /**
     * Find a specific OpenAI bot by its ID.
     *
     * @throws EvolutionApiException
     */
    public function find(string $botId): array
    {
        return $this->service->get("/openai/find/{$botId}/{$this->instanceName}");
    }

    /**
     * Get all OpenAI bots of the instance.
     *
     * @throws EvolutionApiException
     */
    public function findAll(): array
    {
        return $this->service->get("/openai/find/{$this->instanceName}");
    }

    /**
     * Update an existing OpenAI bot.
     *
     * @param array<string, mixed> $data Bot fields (enabled, openaiCredsId, botType and model are required by the API)
     *
     * @throws EvolutionApiException
     */
    public function update(string $botId, array $data): array
    {
        return $this->service->put("/openai/update/{$botId}/{$this->instanceName}", $data);
    }

    /**
     * Delete an OpenAI bot.
     *
     * @throws EvolutionApiException
     */
    public function delete(string $botId): array
    {
        return $this->service->delete("/openai/delete/{$botId}/{$this->instanceName}");
    }

    /**
     * Get the OpenAI credentials stored for the instance.
     *
     * @throws EvolutionApiException
     */
    public function getCredentials(): array
    {
        return $this->service->get("/openai/creds/{$this->instanceName}");
    }

    /**
     * Store OpenAI credentials for the instance.
     *
     * @param string $name Credentials label
     * @param string $apiKey OpenAI API key
     *
     * @throws EvolutionApiException
     */
    public function setCredentials(string $name, string $apiKey): array
    {
        return $this->service->post("/openai/creds/{$this->instanceName}", [
            'name' => $name,
            'apiKey' => $apiKey,
        ]);
    }

    /**
     * Delete stored OpenAI credentials.
     *
     * @throws EvolutionApiException
     */
    public function deleteCredentials(string $credentialsId): array
    {
        return $this->service->delete("/openai/creds/{$credentialsId}/{$this->instanceName}");
    }

    /**
     * Update the default OpenAI settings of the instance.
     *
     * @param array<string, mixed> $settings Settings payload (openaiCredsId, expire, keywordFinish, ...)
     *
     * @throws EvolutionApiException
     */
    public function updateSettings(array $settings): array
    {
        return $this->service->post("/openai/settings/{$this->instanceName}", $settings);
    }

    /**
     * Get the default OpenAI settings of the instance.
     *
     * @throws EvolutionApiException
     */
    public function getSettings(): array
    {
        return $this->service->get("/openai/fetchSettings/{$this->instanceName}");
    }

    /**
     * Change the bot session status for a chat.
     *
     * @param string $remoteJid Chat remote JID
     * @param string $status Session status: opened|paused|closed
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function changeStatus(string $remoteJid, string $status): array
    {
        if (! in_array($status, ['opened', 'paused', 'closed'], true)) {
            throw new InvalidArgumentException('Status must be one of: opened, paused, closed');
        }

        return $this->service->post("/openai/changeStatus/{$this->instanceName}", [
            'remoteJid' => $remoteJid,
            'status' => $status,
        ]);
    }

    /**
     * Get the sessions of an OpenAI bot.
     *
     * @throws EvolutionApiException
     */
    public function findSession(string $botId): array
    {
        return $this->service->get("/openai/fetchSessions/{$botId}/{$this->instanceName}");
    }
}
