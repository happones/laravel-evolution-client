<?php

// tests/Unit/Resources/GroupResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Group;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

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

    #[Test]
    public function it_can_get_all_groups()
    {
        $result = $this->groupResource->all();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('groups', $result);
    }

    #[Test]
    public function it_can_find_group()
    {
        $result = $this->groupResource->find('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_create_group()
    {
        $result = $this->groupResource->create('Test Group', [
            '5511999999999',
            '5511888888888',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_group_subject()
    {
        $result = $this->groupResource->updateSubject('123456789@g.us', 'New Group Name');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_group_description()
    {
        $result = $this->groupResource->updateDescription('123456789@g.us', 'New group description');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_add_participants()
    {
        $result = $this->groupResource->addParticipants('123456789@g.us', [
            '5511777777777',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_remove_participants()
    {
        $result = $this->groupResource->removeParticipants('123456789@g.us', [
            '5511777777777',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_promote_participant_to_admin()
    {
        $result = $this->groupResource->promoteToAdmin('123456789@g.us', '5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_demote_participant_from_admin()
    {
        $result = $this->groupResource->demoteFromAdmin('123456789@g.us', '5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_get_participants()
    {
        $result = $this->groupResource->getParticipants('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_participant()
    {
        $result = $this->groupResource->updateParticipant('123456789@g.us', 'add', ['5511999999999']);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_leave_group()
    {
        $result = $this->groupResource->leave('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_get_invite_code()
    {
        $result = $this->groupResource->getInviteCode('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_join_with_invite_code()
    {
        $result = $this->groupResource->joinWithInviteCode('ABC123');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_group_picture()
    {
        $result = $this->groupResource->updateGroupPicture('123456789@g.us', 'https://example.com/pic.png');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_revoke_invite_code()
    {
        $result = $this->groupResource->revokeInviteCode('123456789@g.us');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_get_invite_info()
    {
        $result = $this->groupResource->inviteInfo('ABC123');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_invite()
    {
        $result = $this->groupResource->sendInvite('123456789@g.us', 'Join us', ['5511999999999']);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_toggle_ephemeral()
    {
        $result = $this->groupResource->toggleEphemeral('123456789@g.us', 86400);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_setting()
    {
        $result = $this->groupResource->updateSetting('123456789@g.us', 'announcement');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_validates_update_setting_action()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->groupResource->updateSetting('123456789@g.us', 'invalid');
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
                    'id' => '123456789@g.us',
                    'subject' => 'Test Group',
                    'creation' => 1678901234,
                    'owner' => '5511999999999@s.whatsapp.net',
                    'desc' => 'Group description',
                ],
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status' => 'success',
            'message' => 'Operation successful',
            'groupId' => '123456789@g.us',
        ]);

        $this->service->method('put')->willReturn([
            'status' => 'success',
            'message' => 'Group updated successfully',
        ]);

        $this->service->method('delete')->willReturn([
            'status' => 'success',
            'message' => 'Left group successfully',
        ]);

        $this->groupResource = new Group($this->service, 'test-instance');
    }
}
