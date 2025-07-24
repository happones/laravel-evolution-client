<?php
// src/Resources/EvolutionBot.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class EvolutionBot
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
     * Creates or updates the Evolution Bot settings for the instance.
     *
     * @param bool   $enabled         Enable Evolution Bot.
     * @param string $apiUrl          API URL for the bot.
     * @param string $apiKey          API Key for the bot.
     * @param string $triggerType     Trigger type, e.g., 'all' or 'keyword'.
     * @param string $triggerOperator Operator logic, e.g., 'contains', 'equals', 'startsWith', 'endsWith', 'regex'.
     * @param string $triggerValue    Trigger value, e.g., 'test'.
     * @param int    $expire          Expiration time for the session (in seconds).
     * @param string $keywordFinish   Keyword to terminate the session.
     * @param int    $delayMessage    Delay time (in ms) for sending messages.
     * @param string $unknownMessage  Message displayed when an unknown input is received.
     * @param bool   $listeningFromMe Listen to messages sent by the bot owner.
     * @param bool   $stopBotFromMe   Stop bot when the owner sends a message.
     * @param bool   $keepOpen        Keep the session open after processing messages.
     * @param int    $debounceTime    Time delay to debounce messages.
     *
     * @throws EvolutionApiException
     *
     * @return array The API response.
     */
    public function create(
        bool $enabled,
        string $apiUrl,
        string $apiKey,
        string $triggerType,
        string $triggerOperator,
        string $triggerValue,
        int $expire,
        string $keywordFinish,
        int $delayMessage,
        string $unknownMessage,
        bool $listeningFromMe,
        bool $stopBotFromMe,
        bool $keepOpen,
        int $debounceTime
    ): array {
        return $this->service->post("/evolutionBot/create/{$this->instanceName}", [
            'enabled'         => $enabled,
            'apiUrl'          => $apiUrl,
            'apiKey'          => $apiKey,
            'triggerType'     => $triggerType,
            'triggerOperator' => $triggerOperator,
            'triggerValue'    => $triggerValue,
            'expire'          => $expire,
            'keywordFinish'   => $keywordFinish,
            'delayMessage'    => $delayMessage,
            'unknownMessage'  => $unknownMessage,
            'listeningFromMe' => $listeningFromMe,
            'stopBotFromMe'   => $stopBotFromMe,
            'keepOpen'        => $keepOpen,
            'debounceTime'    => $debounceTime,
        ]);
    }

    /**
     * Updates the Evolution Bot settings for the instance.
     *
     * @param string      $evolutionBotId  The ID of the Evolution Bot to update.
     * @param bool        $enabled         Enable or disable the Evolution Bot.
     * @param string      $apiUrl          API URL for the bot.
     * @param string      $triggerType     Trigger type, e.g., 'all' or 'keyword'.
     * @param string      $triggerOperator Operator logic, e.g., 'contains', 'equals', 'startsWith', 'endsWith', 'regex'.
     * @param string      $triggerValue    Trigger value, e.g., 'test'.
     * @param int         $expire          Expiration time for the session (in seconds).
     * @param string      $keywordFinish   Keyword to terminate the session.
     * @param int         $delayMessage    Delay time (in ms) for sending messages.
     * @param string      $unknownMessage  Message displayed when an unknown input is received.
     * @param bool        $listeningFromMe Listen to messages sent by the bot owner.
     * @param bool        $stopBotFromMe   Stop bot when the owner sends a message.
     * @param bool        $keepOpen        Keep the session open after processing messages.
     * @param int         $debounceTime    Time delay to debounce messages.
     * @param string|null $apiKey          API Key for authentication (optional).
     * @param array       $ignoreJids      List of JIDs to ignore (optional).
     *
     * @throws EvolutionApiException
     *
     * @return array The API response.
     */
    public function update(
        string $evolutionBotId,
        bool $enabled,
        string $apiUrl,
        string $triggerType,
        string $triggerOperator,
        string $triggerValue,
        int $expire,
        string $keywordFinish,
        int $delayMessage,
        string $unknownMessage,
        bool $listeningFromMe,
        bool $stopBotFromMe,
        bool $keepOpen,
        int $debounceTime,
        ?string $apiKey = null,
        array $ignoreJids = []
    ): array {
        return $this->service->put("/evolutionBot/update/{$evolutionBotId}/{$this->instanceName}", [
            'enabled'         => $enabled,
            'apiUrl'          => $apiUrl,
            'apiKey'          => $apiKey,
            'triggerType'     => $triggerType,
            'triggerOperator' => $triggerOperator,
            'triggerValue'    => $triggerValue,
            'expire'          => $expire,
            'keywordFinish'   => $keywordFinish,
            'delayMessage'    => $delayMessage,
            'unknownMessage'  => $unknownMessage,
            'listeningFromMe' => $listeningFromMe,
            'stopBotFromMe'   => $stopBotFromMe,
            'keepOpen'        => $keepOpen,
            'debounceTime'    => $debounceTime,
            'ignoreJids'      => $ignoreJids,
        ]);
    }

    /**
     * Deletes an Evolution Bot from the instance.
     *
     * @param string $evolutionBotId The ID of the Evolution Bot to delete.
     *
     * @throws EvolutionApiException
     *
     * @return array The API response.
     */
    public function destroy(string $evolutionBotId): array
    {
        return $this->service->delete("/evolutionBot/delete/{$evolutionBotId}/{$this->instanceName}");
    }
}
