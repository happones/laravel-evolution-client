<?php
// tests/Unit/Resources/ProfileResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Resources\Profile;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class ProfileResourceTest extends TestCase
{
    /**
     * @var Profile
     */
    protected $profileResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_fetch_business_profile()
    {
        $result = $this->profileResource->fetchBusinessProfile('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_fetch_profile()
    {
        $result = $this->profileResource->fetchProfile('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_profile_name()
    {
        $result = $this->profileResource->updateProfileName('New Name');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_profile_status()
    {
        $result = $this->profileResource->updateProfileStatus('Available');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_profile_picture()
    {
        $result = $this->profileResource->updateProfilePicture('https://example.com/picture.jpg');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_remove_profile_picture()
    {
        $result = $this->profileResource->removeProfilePicture();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_fetch_privacy_settings()
    {
        $result = $this->profileResource->fetchPrivacySettings();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_privacy_settings()
    {
        $result = $this->profileResource->updatePrivacySettings(
            'all',
            'contacts',
            'contacts',
            'all',
            'contacts',
            'contacts'
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

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Profile operation successful',
        ]);

        $this->service->method('get')->willReturn([
            'status'   => 'success',
            'settings' => [
                'readreceipts' => 'all',
                'profile'      => 'contacts',
                'status'       => 'contacts',
                'online'       => 'all',
                'last'         => 'contacts',
                'groupadd'     => 'contacts',
            ],
        ]);

        $this->service->method('delete')->willReturn([
            'status'  => 'success',
            'message' => 'Profile picture removed',
        ]);

        $this->profileResource = new Profile($this->service, 'test-instance');
    }
}
