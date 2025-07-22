<?php
// tests/Unit/Resources/GroupResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Resources\Group;
use Happones\LaravelEvolutionClient\Services\EvolutionService;

class GroupResourceTest extends TestCase
{
    /**
     * @var Group
     */
    protected $groupResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    /** @test */
    public function it_can_get_all_groups()
    {
        $result = $this->groupResource->all();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('groups', $result);
    }

    /** @test */
    public function it_can_find_group()
    {
        $result = $this->groupResource->find('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_create_group()
    {
        $result = $this->groupResource->create('Test Group', [
            '5511999999999',
            '5511888888888',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_group_subject()
    {
        $result = $this->groupResource->updateSubject('123456789@g.us', 'New Group Name');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_update_group_description()
    {
        $result = $this->groupResource->updateDescription('123456789@g.us', 'New group description');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_add_participants()
    {
        $result = $this->groupResource->addParticipants('123456789@g.us', [
            '5511777777777',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_remove_participants()
    {
        $result = $this->groupResource->removeParticipants('123456789@g.us', [
            '5511777777777',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_promote_participant_to_admin()
    {
        $result = $this->groupResource->promoteToAdmin('123456789@g.us', '5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_demote_participant_from_admin()
    {
        $result = $this->groupResource->demoteFromAdmin('123456789@g.us', '5511999999999');

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
            'groups' => [
                [
                    'id'       => '123456789@g.us',
                    'subject'  => 'Test Group',
                    'creation' => 1678901234,
                    'owner'    => '5511999999999@c.us',
                    'desc'     => 'Group description',
                ],
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Operation successful',
            'groupId' => '123456789@g.us',
        ]);

        $this->service->method('put')->willReturn([
            'status'  => 'success',
            'message' => 'Group updated successfully',
        ]);

        $this->groupResource = new Group($this->service, 'test-instance');
    }
}
