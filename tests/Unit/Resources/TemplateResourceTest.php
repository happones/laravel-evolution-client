<?php
// tests/Unit/Resources/TemplateResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Resources\Template;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class TemplateResourceTest extends TestCase
{
    /**
     * @var Template
     */
    protected $templateResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_create_template()
    {
        $result = $this->templateResource->create(
            'my_template',
            'MARKETING',
            'en_US',
            [
                [
                    'type'    => 'BODY',
                    'text'    => 'Hello {{1}}, welcome to our service!',
                    'example' => [
                        'body_text' => [
                            ['John Doe'],
                        ],
                    ],
                ],
                [
                    'type'    => 'BUTTONS',
                    'buttons' => [
                        [
                            'type' => 'QUICK_REPLY',
                            'text' => 'Yes, please',
                        ],
                        [
                            'type' => 'QUICK_REPLY',
                            'text' => 'No, thanks',
                        ],
                    ],
                ],
            ]
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_find_templates()
    {
        $result = $this->templateResource->find();

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
            'message' => 'Template created successfully',
        ]);

        $this->service->method('get')->willReturn([
            'status'    => 'success',
            'templates' => [
                [
                    'name'     => 'my_template',
                    'status'   => 'APPROVED',
                    'category' => 'MARKETING',
                    'language' => 'en_US',
                ],
            ],
        ]);

        $this->templateResource = new Template($this->service, 'test-instance');
    }
}
