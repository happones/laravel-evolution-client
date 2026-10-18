<?php

// tests/Unit/Resources/InstanceResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Instance;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function it_can_create_instance()
    {
        $result = $this->instanceResource->createInstance('test');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_get_qr_code()
    {
        $result = $this->instanceResource->getQrCode();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('qrcode', $result);
    }

    #[Test]
    public function it_can_check_if_instance_is_connected()
    {
        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('get')->willReturn([
            'instance' => [
                'instanceName' => 'test-instance',
                'state' => 'open',
            ],
        ]);

        $this->instanceResource = new Instance($this->service, 'test-instance');

        $this->assertTrue($this->instanceResource->isConnected());
    }

    #[Test]
    public function it_can_set_presence()
    {
        $result = $this->instanceResource->setPresence('available');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_validates_presence_value()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->instanceResource->setPresence('invalid');
    }

    #[Test]
    public function it_can_get_status()
    {
        $result = $this->instanceResource->getStatus();

        $this->assertIsArray($result);
        $this->assertEquals('connected', $result['status']);
    }

    #[Test]
    public function it_can_connect()
    {
        $result = $this->instanceResource->connect();

        $this->assertIsArray($result);
        $this->assertEquals('connected', $result['status']);
    }

    #[Test]
    public function it_requests_a_pairing_code_with_the_phone_number()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('get')
            ->with('/instance/connect/test-instance', ['number' => '5215512345678'])
            ->willReturn(['pairingCode' => 'WZYEH1YY', 'code' => '2@abc', 'count' => 1]);

        $code = (new Instance($service, 'test-instance'))->getPairingCode('+52 1 55 1234 5678');

        $this->assertSame('WZYEH1YY', $code);
    }

    #[Test]
    public function it_returns_null_when_no_pairing_code_is_issued()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->method('get')->willReturn(['code' => '2@abc']);

        $this->assertNull((new Instance($service, 'test-instance'))->getPairingCode('5215512345678'));
    }

    #[Test]
    public function it_validates_the_pairing_code_number()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->instanceResource->getPairingCode('not-a-number');
    }

    #[Test]
    public function it_can_disconnect()
    {
        $result = $this->instanceResource->disconnect();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_delete_instance()
    {
        $result = $this->instanceResource->delete();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_restart_instance()
    {
        $result = $this->instanceResource->restart();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_set_webhook()
    {
        $result = $this->instanceResource->setWebhook(
            'https://example.com/webhook',
            ['message', 'message.ack']
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_sends_webhook_config_nested_under_the_webhook_key()
    {
        $service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service->expects($this->once())
            ->method('post')
            ->with(
                '/webhook/set/test-instance',
                $this->callback(function ($payload) {
                    return isset($payload['webhook'])
                        && $payload['webhook']['url'] === 'https://example.com/webhook'
                        && $payload['webhook']['enabled'] === true
                        && $payload['webhook']['events'] === ['MESSAGES_UPSERT']
                        && array_key_exists('webhookByEvents', $payload['webhook'])
                        && array_key_exists('webhookBase64', $payload['webhook']);
                })
            )
            ->willReturn(['status' => 'success']);

        $resource = new Instance($service, 'test-instance');
        $resource->setWebhook('https://example.com/webhook', ['MESSAGES_UPSERT']);
    }

    #[Test]
    public function it_can_get_webhook()
    {
        $result = $this->instanceResource->getWebhook();

        $this->assertIsArray($result);
    }

    #[Test]
    public function it_can_get_connection_state()
    {
        $result = $this->instanceResource->connectionState();

        $this->assertIsArray($result);
        $this->assertEquals('connected', $result['status']);
    }

    #[Test]
    public function it_can_fetch_instances()
    {
        $result = $this->instanceResource->fetchInstances();

        $this->assertIsArray($result);
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
            'status' => 'success',
            'message' => 'Operation successful',
        ]);

        $this->service->method('put')->willReturn([
            'status' => 'success',
            'message' => 'Operation successful',
        ]);

        $this->service->method('delete')->willReturn([
            'status' => 'success',
            'message' => 'Instance operation successful',
        ]);

        $this->instanceResource = new Instance($this->service, 'test-instance');
    }
}
