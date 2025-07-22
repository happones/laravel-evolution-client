<?php
// tests/Unit/Resources/ProxyResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Resources\Proxy;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class ProxyResourceTest extends TestCase
{
    /**
     * @var Proxy
     */
    protected $proxyResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_set_proxy()
    {
        $result = $this->proxyResource->set(
            true,
            '127.0.0.1',
            '8080',
            'http',
            'username',
            'password'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_find_proxy_settings()
    {
        $result = $this->proxyResource->find();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Proxy settings updated',
        ]);

        $this->service->method('get')->willReturn([
            'status' => 'success',
            'proxy'  => [
                'enabled'  => true,
                'host'     => '127.0.0.1',
                'port'     => '8080',
                'protocol' => 'http',
                'username' => 'username',
                'password' => '********',
            ],
        ]);

        $this->proxyResource = new Proxy($this->service, 'test-instance');
    }
}
