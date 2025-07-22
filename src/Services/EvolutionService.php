<?php
// src/Services/EvolutionService.php

namespace Happones\LaravelEvolutionClient\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;

class EvolutionService
{
    /**
     * @var Client The HTTP client
     */
    protected Client $client;

    /**
     * @var string The base URL for the Evolution API
     */
    protected string $baseUrl;

    /**
     * @var string The API key
     */
    protected string $apiKey;

    /**
     * Create a new EvolutionService instance.
     *
     * @param string $baseUrl
     * @param string $apiKey
     * @param int    $timeout
     */
    public function __construct(string $baseUrl, string $apiKey, int $timeout = 30)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey  = $apiKey;
        $this->client  = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => $timeout,
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'apikey'       => $this->apiKey,
            ],
        ]);
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * Get the API key.
     *
     * @return string
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the HTTP client.
     *
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * Make a GET request to the Evolution API.
     *
     * @param string $endpoint
     * @param array  $queryParams
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function get(string $endpoint, array $queryParams = []): array
    {
        return $this->request('GET', $endpoint, ['query' => $queryParams]);
    }

    /**
     * Make a request to the Evolution API.
     *
     * @param string $method
     * @param string $endpoint
     * @param array  $options
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    protected function request(string $method, string $endpoint, array $options = []): array
    {
        $url = ltrim($endpoint, '/');

        try {
            $response = $this->client->request($method, $url, $options);
            $body     = $response->getBody()->getContents();
            $data     = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new EvolutionApiException('Invalid JSON response from API', 500);
            }

            // Check for API error response
            if (isset($data['error']) || (isset($data['status']) && $data['status'] === 'error')) {
                $message = $data['error'] ?? $data['message'] ?? 'Unknown API error';
                $code    = $data['code']  ?? 400;

                throw new EvolutionApiException($message, $code);
            }

            return $data ?? [];
        } catch (GuzzleException $e) {
            $message    = $e->getMessage();
            $statusCode = $e->getCode();

            // Try to parse error response
            if ($e->hasResponse()) {
                $errorBody = $e->getResponse()->getBody()->getContents();
                $errorData = json_decode($errorBody, true);

                if (is_array($errorData) && isset($errorData['error'])) {
                    $message = $errorData['error'];
                }
            }

            throw new EvolutionApiException($message, $statusCode, $e);
        } catch (Exception $e) {
            throw new EvolutionApiException('Unexpected error: ' . $e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Make a POST request to the Evolution API.
     *
     * @param string $endpoint
     * @param array  $data
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function post(string $endpoint, array $data = []): array
    {
        return $this->request('POST', $endpoint, ['json' => $data]);
    }

    /**
     * Make a PUT request to the Evolution API.
     *
     * @param string $endpoint
     * @param array  $data
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function put(string $endpoint, array $data = []): array
    {
        return $this->request('PUT', $endpoint, ['json' => $data]);
    }

    /**
     * Make a DELETE request to the Evolution API.
     *
     * @param string $endpoint
     * @param array  $queryParams
     *
     * @throws EvolutionApiException
     *
     * @return array
     */
    public function delete(string $endpoint, array $queryParams = []): array
    {
        return $this->request('DELETE', $endpoint, ['query' => $queryParams]);
    }
}
