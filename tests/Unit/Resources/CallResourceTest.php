<?php
// tests/Unit/Resources/CallResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Call;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class CallResourceTest extends TestCase
{
    /**
     * @var Call
     */
    protected $callResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_make_fake_voice_call()
    {
        $result = $this->callResource->fakeCall('5511999999999', false, 30);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_make_fake_video_call()
    {
        $result = $this->callResource->fakeCall('5511999999999', true, 45);

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
            'message' => 'Fake call sent',
        ]);

        $this->callResource = new Call($this->service, 'test-instance');
    }
}
