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
     * Get business catalog.
     *
     * @param string      $number
     * @param int|null    $limit
     * @param string|null $cursor
     *
     * @throws EvolutionApiException
     *
     * @return array
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
     * @param string      $number
     * @param int|null    $limit
     * @param string|null $cursor
     *
     * @throws EvolutionApiException
     *
     * @return array
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
     * Format phone number to be used with the API.
     *
     * @param string $phoneNumber
     *
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // If it already looks like a formatted JID, return it as is
        if (str_contains($phoneNumber, '@')) {
            return $phoneNumber;
        }

        // Remove any non-digit characters
        $number = preg_replace('/\D/', '', $phoneNumber);

        // Add @ to create a valid recipient id for the API
        return $number . '@c.us';
    }
}
