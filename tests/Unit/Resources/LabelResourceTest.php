<?php
// tests/Unit/Resources/LabelResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Label;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class LabelResourceTest extends TestCase
{
    /**
     * @var Label
     */
    protected $labelResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_find_labels()
    {
        $result = $this->labelResource->findLabels();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('labels', $result);
    }

    /** @test */
    public function it_can_add_label()
    {
        $result = $this->labelResource->addLabel('5511999999999', 'label-id-123');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_remove_label()
    {
        $result = $this->labelResource->removeLabel('5511999999999', 'label-id-123');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_handle_label()
    {
        $result = $this->labelResource->handleLabel('5511999999999', 'label-id-123', 'add');

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
            'status' => 'success',
            'labels' => [
                [
                    'id'    => 'label-id-123',
                    'name'  => 'Important',
                    'color' => 4,
                ],
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Label operation successful',
        ]);

        $this->labelResource = new Label($this->service, 'test-instance');
    }
}
