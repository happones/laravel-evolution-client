<?php

// src/Resources/Business.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class Business
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
     * Create a new Business resource instance.
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
     * Get business catalog.
     *
     *
     * @throws EvolutionApiException
     */
    public function getCatalog(string $number, ?int $limit = null, ?string $cursor = null): array
    {
        $formattedNumber = $this->formatPhoneNumber($number);

        $payload = [
            'number' => $formattedNumber,
        ];

        if ($limit !== null) {
            $payload['limit'] = $limit;
        }

        if ($cursor !== null) {
            $payload['cursor'] = $cursor;
        }

        return $this->service->post("/business/getCatalog/{$this->instanceName}", $payload);
    }

    /**
     * Get business collections.
     *
     *
     * @throws EvolutionApiException
     */
    public function getCollections(string $number, ?int $limit = null, ?string $cursor = null): array
    {
        $formattedNumber = $this->formatPhoneNumber($number);

        $payload = [
            'number' => $formattedNumber,
        ];

        if ($limit !== null) {
            $payload['limit'] = $limit;
        }

        if ($cursor !== null) {
            $payload['cursor'] = $cursor;
        }

        return $this->service->post("/business/getCollections/{$this->instanceName}", $payload);
    }

    /**
     * Format a phone number for the API.
     *
     * Evolution API v2 expects the bare number with country code (it builds the
     * "@s.whatsapp.net" JID itself), so only digits are kept. Values that are
     * already a JID (e.g. "...@g.us", "...@s.whatsapp.net", "...@lid") are
     * passed through unchanged.
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        if (str_contains($phoneNumber, '@')) {
            return $phoneNumber;
        }

        return (string) preg_replace('/\D/', '', $phoneNumber);
    }
}
