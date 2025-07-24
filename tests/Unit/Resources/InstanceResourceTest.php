<?php
// tests/Unit/Resources/InstanceResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Instance;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class InstanceResourceTest extends TestCase
{
    /**
     * @var Instance
     */
    protected $instanceResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_create_instance()
    {
        $result = $this->instanceResource->createInstance('test');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_qr_code()
    {
        $result = $this->instanceResource->getQrCode();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_check_if_instance_is_connected()
    {
        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('get')->willReturn([
            'status' => 'connected',
        ]);

        $this->instanceResource = new Instance($this->service, 'test-instance');

        $this->assertTrue($this->instanceResource->isConnected());
    }

    /** @test */
    public function it_can_get_status()
    {
        $result = $this->instanceResource->getStatus();

        $this->assertIsArray($result);
        $this->assertEquals('connected', $result['status']);
    }

    /** @test */
    public function it_can_connect()
    {
        $result = $this->instanceResource->connect();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_disconnect()
    {
        $result = $this->instanceResource->disconnect();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_delete_instance()
    {
        $result = $this->instanceResource->delete();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_restart_instance()
    {
        $result = $this->instanceResource->restart();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_set_webhook()
    {
        $result = $this->instanceResource->setWebhook(
            'https://example.com/webhook',
            ['message', 'message.ack']
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('get')->willReturn([
            'status' => 'connected',
            'qrcode' => [
                'base64' => 'data:image/png;base64,...',
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Operation successful',
        ]);

        $this->service->method('delete')->willReturn([
            'status'  => 'success',
            'message' => 'Instance operation successful',
        ]);

        $this->instanceResource = new Instance($this->service, 'test-instance');
    }
}
