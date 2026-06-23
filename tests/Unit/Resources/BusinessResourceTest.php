<?php
// tests/Unit/Resources/BusinessResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Business;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class BusinessResourceTest extends TestCase
{
    /**
     * @var Business
     */
    protected $businessResource;

    /**
     * @var EvolutionService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->businessResource = new Business($this->service, 'test-instance');
    }

    /** @test */
    public function it_can_get_catalog_without_optional_parameters()
    {
        $this->service->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('/business/getCatalog/test-instance'),
                $this->equalTo(['number' => '5511999999999@c.us'])
            )
            ->willReturn([
                'status'  => 'success',
                'catalog' => [],
            ]);

        $result = $this->businessResource->getCatalog('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_catalog_with_optional_parameters()
    {
        $this->service->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('/business/getCatalog/test-instance'),
                $this->equalTo([
                    'number' => '5511999999999@c.us',
                    'limit'  => 20,
                    'cursor' => 'next-cursor-token',
                ])
            )
            ->willReturn([
                'status'  => 'success',
                'catalog' => [],
            ]);

        $result = $this->businessResource->getCatalog('5511999999999', 20, 'next-cursor-token');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_collections_without_optional_parameters()
    {
        $this->service->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('/business/getCollections/test-instance'),
                $this->equalTo(['number' => '5511999999999@c.us'])
            )
            ->willReturn([
                'status'      => 'success',
                'collections' => [],
            ]);

        $result = $this->businessResource->getCollections('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_collections_with_optional_parameters()
    {
        $this->service->expects($this->once())
            ->method('post')
            ->with(
                $this->equalTo('/business/getCollections/test-instance'),
                $this->equalTo([
                    'number' => '5511999999999@c.us',
                    'limit'  => 10,
                    'cursor' => 'some-cursor',
                ])
            )
            ->willReturn([
                'status'      => 'success',
                'collections' => [],
            ]);

        $result = $this->businessResource->getCollections('5511999999999', 10, 'some-cursor');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }
}
