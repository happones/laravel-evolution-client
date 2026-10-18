<?php

// src/Resources/Instance.php

namespace Happones\LaravelEvolutionClient\Resources;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;

class Instance
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
     * Get the current instance name.
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
     * Create a new instance.
     *
     * @param string $instanceName The instance name
     * @param string $integration WhatsApp engine: WHATSAPP-BAILEYS|WHATSAPP-BUSINESS
     * @param string|null $token Optional API key (created dynamically when empty)
     * @param string|null $number Optional owner number with country code
     * @param bool|null $qrcode Whether to create a QR Code automatically after creation
     *
     * @throws EvolutionApiException
     */
    public function createInstance(
        string $instanceName,
        string $integration = 'WHATSAPP-BAILEYS',
        ?string $token = null,
        ?string $number = null,
        ?bool $qrcode = null
    ): array {
        $payload = [
            'instanceName' => $instanceName,
            'integration' => $integration,
        ];

        if ($token !== null) {
            $payload['token'] = $token;
        }

        if ($number !== null) {
            $payload['number'] = $number;
        }

        if ($qrcode !== null) {
            $payload['qrcode'] = $qrcode;
        }

        return $this->service->post('/instance/create', $payload);
    }

    /**
     * Get the QR code for the instance.
     *
     * @throws EvolutionApiException
     */
    public function getQrCode(): array
    {
        return $this->connect();
    }

    /**
     * Check if the instance is connected.
     *
     * @throws EvolutionApiException
     */
    public function isConnected(): bool
    {
        $status = $this->getStatus();

        $state = $status['instance']['state'] ?? $status['state'] ?? null;

        return $state === 'open';
    }

    /**
     * Get the connection status of the instance.
     *
     * @throws EvolutionApiException
     */
    public function getStatus(): array
    {
        return $this->service->get("/instance/connectionState/{$this->instanceName}");
    }

    /**
     * Connect the instance.
     *
     * Without a number the response carries the QR code ("code"/"base64"). With a
     * number, Evolution also returns a "pairingCode" to link the device by phone.
     *
     * @param string|null $number Phone number with country code, to request a pairing code
     *
     * @throws EvolutionApiException
     */
    public function connect(?string $number = null): array
    {
        $query = $number !== null ? ['number' => preg_replace('/\D/', '', $number)] : [];

        return $this->service->get("/instance/connect/{$this->instanceName}", $query);
    }

    /**
     * Get a pairing code to link the instance by phone number instead of QR.
     *
     * @param string $number Phone number with country code
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function getPairingCode(string $number): ?string
    {
        if (preg_replace('/\D/', '', $number) === '') {
            throw new InvalidArgumentException('Phone number is required');
        }

        $response = $this->connect($number);

        return $response['pairingCode'] ?? null;
    }

    /**
     * Disconnect the instance.
     *
     * @throws EvolutionApiException
     */
    public function disconnect(): array
    {
        return $this->service->delete("/instance/logout/{$this->instanceName}");
    }

    /**
     * Delete the instance.
     *
     * @throws EvolutionApiException
     */
    public function delete(): array
    {
        return $this->service->delete("/instance/delete/{$this->instanceName}");
    }

    /**
     * Restart the instance.
     *
     * @throws EvolutionApiException
     */
    public function restart(): array
    {
        // Evolution API v2 (deployed builds) expose restart as POST; the OpenAPI
        // spec lists PUT, but the running server returns 404 for PUT.
        return $this->service->post("/instance/restart/{$this->instanceName}");
    }

    /**
     * Set the presence for the instance.
     *
     * @param string $presence Presence value: available|unavailable
     *
     * @throws EvolutionApiException
     * @throws InvalidArgumentException
     */
    public function setPresence(string $presence): array
    {
        if (! in_array($presence, ['available', 'unavailable'], true)) {
            throw new InvalidArgumentException('Presence must be one of: available, unavailable');
        }

        return $this->service->post("/instance/setPresence/{$this->instanceName}", [
            'presence' => $presence,
        ]);
    }

    /**
     * Set the webhook URL for the instance.
     *
     * @param string $url Webhook URL
     * @param array<int, string> $events Events to be sent to the webhook
     * @param bool $enabled Whether the webhook is enabled
     * @param array<string, string> $headers Optional custom headers
     * @param bool $base64 Whether to send files in base64 when available
     * @param bool $webhookByEvents Whether to enable webhook by events
     *
     * @throws EvolutionApiException
     */
    public function setWebhook(
        string $url,
        array $events = [],
        bool $enabled = true,
        array $headers = [],
        bool $base64 = false,
        bool $webhookByEvents = false
    ): array {
        $webhook = [
            'enabled' => $enabled,
            'url' => $url,
            'webhookByEvents' => $webhookByEvents,
            'webhookBase64' => $base64,
            'events' => $events,
        ];

        if (! empty($headers)) {
            $webhook['headers'] = $headers;
        }

        // Evolution API v2 requires the configuration nested under "webhook".
        return $this->service->post("/webhook/set/{$this->instanceName}", ['webhook' => $webhook]);
    }

    /**
     * Get the webhook configuration for the instance.
     *
     * @throws EvolutionApiException
     */
    public function getWebhook(): array
    {
        return $this->service->get("/webhook/find/{$this->instanceName}");
    }

    /**
     * Get the connection state of the instance.
     *
     * @throws EvolutionApiException
     */
    public function connectionState(): array
    {
        return $this->service->get("/instance/connectionState/{$this->instanceName}");
    }

    /**
     * Fetch all instances.
     *
     * @throws EvolutionApiException
     */
    public function fetchInstances(): array
    {
        return $this->service->get('/instance/fetchInstances');
    }
}
