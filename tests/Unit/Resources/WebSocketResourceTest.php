<?php
// tests/Unit/Resources/WebSocketResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use GuzzleHttp\Handler\MockHandler;
use Happones\LaravelEvolutionClient\Resources\WebSocket;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class WebSocketResourceTest extends TestCase
{
    /**
     * @var WebSocket
     */
    protected $webSocketResource;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_set_websocket_config()
    {
        $events = ['message', 'message.ack'];
        $result = $this->webSocketResource->setWebSocket(true, $events);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_find_websocket_config()
    {
        $result = $this->webSocketResource->findWebSocket();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_create_websocket_client()
    {
        // Define PHPUNIT_RUNNING constant to make createClient return null for testing
        if (! defined('PHPUNIT_RUNNING')) {
            define('PHPUNIT_RUNNING', true);
        }

        $client = $this->webSocketResource->createClient();
        $this->assertNull($client);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'WebSocket settings updated',
        ]);

        $this->service->method('get')->willReturn([
            'status'    => 'success',
            'websocket' => [
                'enabled' => true,
                'events'  => ['message', 'message.ack'],
            ],
        ]);

        $this->webSocketResource = new WebSocket($this->service, 'test-instance');
    }
}
