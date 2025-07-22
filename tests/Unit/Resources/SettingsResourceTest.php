<?php
// tests/Unit/Resources/SettingsResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Settings;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\TestCase;

class SettingsResourceTest extends TestCase
{
    /**
     * @var Settings
     */
    protected $settingsResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_set_settings()
    {
        $result = $this->settingsResource->set(
            true,
            'I do not accept calls',
            false,
            true,
            false,
            false,
            false
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_find_settings()
    {
        $result = $this->settingsResource->find();

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
            'message' => 'Settings updated',
        ]);

        $this->service->method('get')->willReturn([
            'status'   => 'success',
            'settings' => [
                'rejectCall'      => true,
                'msgCall'         => 'I do not accept calls',
                'groupsIgnore'    => false,
                'alwaysOnline'    => true,
                'readMessages'    => false,
                'syncFullHistory' => false,
                'readStatus'      => false,
            ],
        ]);

        $this->settingsResource = new Settings($this->service, 'test-instance');
    }
}
