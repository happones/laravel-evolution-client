<?php

// src/EvolutionApiClient.php

namespace Happones\LaravelEvolutionClient;

use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Resources\Business;
use Happones\LaravelEvolutionClient\Resources\Call;
use Happones\LaravelEvolutionClient\Resources\Chat;
use Happones\LaravelEvolutionClient\Resources\EvolutionBot;
use Happones\LaravelEvolutionClient\Resources\Group;
use Happones\LaravelEvolutionClient\Resources\Instance;
use Happones\LaravelEvolutionClient\Resources\Label;
use Happones\LaravelEvolutionClient\Resources\Message;
use Happones\LaravelEvolutionClient\Resources\OpenAIBot;
use Happones\LaravelEvolutionClient\Resources\Profile;
use Happones\LaravelEvolutionClient\Resources\Proxy;
use Happones\LaravelEvolutionClient\Resources\Settings;
use Happones\LaravelEvolutionClient\Resources\Template;
use Happones\LaravelEvolutionClient\Resources\WebSocket;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class EvolutionApiClient
{
    /**
     * @var Chat The Chat resource
     */
    public Chat $chat;

    /**
     * @var Group The Group resource
     */
    public Group $group;

    /**
     * @var Message The Message resource
     */
    public Message $message;

    /**
     * @var Instance The Instance resource
     */
    public Instance $instance;

    /**
     * @var Call The Call resource
     */
    public Call $call;

    /**
     * @var Label The Label resource
     */
    public Label $label;

    /**
     * @var Profile The Profile resource
     */
    public Profile $profile;

    /**
     * @var WebSocket The WebSocket resource
     */
    public WebSocket $websocket;

    /**
     * @var string The instance name
     */
    protected string $instanceName;

    /**
     * @var EvolutionService The Evolution API service
     */
    protected EvolutionService $service;

    /**
     * @var Template The Template resource
     */
    public Template $template;

    /**
     * @var Proxy The Proxy resource
     */
    public Proxy $proxy;

    /**
     * @var Settings The Settings resource
     */
    public Settings $settings;

    /**
     * @var OpenAIBot The OpenAIBot resource
     */
    public OpenAIBot $openAIBot;

    /**
     * @var EvolutionBot The EvolutionBot resource
     */
    public EvolutionBot $evolutionBot;

    /**
     * @var Business The Business resource
     */
    public Business $business;

    /**
     * Create a new EvolutionApiClient instance.
     */
    public function __construct(EvolutionService $service, string $instanceName = 'default')
    {
        $this->service = $service;
        $this->instanceName = $instanceName;

        // Initialize resources
        $this->chat = new Chat($service, $instanceName);
        $this->group = new Group($service, $instanceName);
        $this->message = new Message($service, $instanceName);
        $this->instance = new Instance($service, $instanceName);
        $this->call = new Call($service, $instanceName);
        $this->label = new Label($service, $instanceName);
        $this->profile = new Profile($service, $instanceName);
        $this->websocket = new WebSocket($service, $instanceName);
        $this->template = new Template($service, $instanceName);
        $this->proxy = new Proxy($service, $instanceName);
        $this->settings = new Settings($service, $instanceName);
        $this->openAIBot = new OpenAIBot($service, $instanceName);
        $this->evolutionBot = new EvolutionBot($service, $instanceName);
        $this->business = new Business($service, $instanceName);
    }

    /**
     * Get a client bound to the given instance.
     *
     * Returns a new, isolated client instead of mutating this one, so a
     * long-lived container singleton (queue workers, Octane) never leaks one
     * instance's name into another job or request.
     */
    public function instance(string $instanceName): self
    {
        return new self($this->service, $instanceName);
    }

    /**
     * Get the instance name this client is bound to.
     */
    public function getInstanceName(): string
    {
        return $this->instanceName;
    }

    /**
     * Create a new instance.
     *
     * @throws EvolutionApiException
     */
    public function createInstance(string $name): array
    {
        return $this->instance($name)->instance->createInstance($name);
    }

    /**
     * Get the QR code for the instance.
     *
     * @throws EvolutionApiException
     */
    public function getQrCode(): array
    {
        return $this->instance->getQrCode();
    }

    /**
     * Get a pairing code to link the instance by phone number instead of QR.
     *
     * @param string $phoneNumber Phone number with country code
     *
     * @throws EvolutionApiException
     */
    public function getPairingCode(string $phoneNumber): ?string
    {
        return $this->instance->getPairingCode($phoneNumber);
    }

    /**
     * Check if the instance is connected.
     *
     * @throws EvolutionApiException
     */
    public function isConnected(): bool
    {
        return $this->instance->isConnected();
    }

    /**
     * Disconnect the instance.
     *
     * @throws EvolutionApiException
     */
    public function disconnect(): array
    {
        return $this->instance->disconnect();
    }

    /**
     * Send a text message.
     *
     *
     * @throws EvolutionApiException
     */
    public function sendText(string $phoneNumber, string $message): array
    {
        return $this->message->sendText($phoneNumber, $message);
    }
}
