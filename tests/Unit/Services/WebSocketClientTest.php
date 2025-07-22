<?php
// tests/Unit/Services/WebSocketClientTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Services;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Services\WebSocketClient;

class WebSocketClientTest extends TestCase
{
    /** @test */
    public function it_can_be_instantiated()
    {
        // Create a simple class extending WebSocketClient for testing
        $webSocketClient = new class('ws://localhost:8080', 'instance-id', 'api-token') extends WebSocketClient {
            // Override constructor to avoid React EventLoop initialization
            public function __construct($baseUrl, $instanceId, $apiToken)
            {
                $this->baseUrl    = $baseUrl;
                $this->instanceId = $instanceId;
                $this->apiToken   = $apiToken;
                $this->handlers   = [];
            }
        };

        $this->assertSame('ws://localhost:8080', $webSocketClient->baseUrl);
        $this->assertSame('instance-id', $webSocketClient->instanceId);
        $this->assertSame('api-token', $webSocketClient->apiToken);
    }

    /** @test */
    public function it_can_register_event_handlers()
    {
        // Create a simple class extending WebSocketClient for testing
        $webSocketClient = new class('ws://localhost:8080', 'instance-id', 'api-token') extends WebSocketClient {
            // For testing, make properties and handlers public
            public $baseUrl;
            public $instanceId;
            public $apiToken;
            public $handlers = [];

            public function __construct($baseUrl, $instanceId, $apiToken)
            {
                $this->baseUrl    = $baseUrl;
                $this->instanceId = $instanceId;
                $this->apiToken   = $apiToken;
            }
        };

        $eventHandler = function ($data) {
            return $data;
        };

        $result = $webSocketClient->on('message', $eventHandler);

        $this->assertSame($webSocketClient, $result);
        $this->assertArrayHasKey('message', $webSocketClient->handlers);
        $this->assertSame($eventHandler, $webSocketClient->handlers['message']);
    }

    /** @test */
    public function it_can_register_multiple_event_handlers()
    {
        // Create a simple class extending WebSocketClient for testing
        $webSocketClient = new class('ws://localhost:8080', 'instance-id', 'api-token') extends WebSocketClient {
            // For testing, make properties and handlers public
            public $baseUrl;
            public $instanceId;
            public $apiToken;
            public $handlers = [];

            public function __construct($baseUrl, $instanceId, $apiToken)
            {
                $this->baseUrl    = $baseUrl;
                $this->instanceId = $instanceId;
                $this->apiToken   = $apiToken;
            }
        };

        $messageHandler = function ($data) {
            return 'message: ' . json_encode($data);
        };

        $ackHandler = function ($data) {
            return 'ack: ' . json_encode($data);
        };

        $webSocketClient->on('message', $messageHandler);
        $webSocketClient->on('message.ack', $ackHandler);

        $this->assertCount(2, $webSocketClient->handlers);
        $this->assertArrayHasKey('message', $webSocketClient->handlers);
        $this->assertArrayHasKey('message.ack', $webSocketClient->handlers);
        $this->assertSame($messageHandler, $webSocketClient->handlers['message']);
        $this->assertSame($ackHandler, $webSocketClient->handlers['message.ack']);
    }
}
