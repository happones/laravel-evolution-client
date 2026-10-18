<?php

// tests/Unit/Resources/ChatResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Chat;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ChatResourceTest extends TestCase
{
    /**
     * @var Chat
     */
    protected $chatResource;

    /**
     * @var EvolutionService
     */
    protected $service;

    #[Test]
    public function it_can_get_all_chats()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/chat/findChats/test-instance', [])
            ->willReturn([['remoteJid' => '5511999999999@s.whatsapp.net']]);

        $result = (new Chat($service, 'test-instance'))->all();

        $this->assertSame([['remoteJid' => '5511999999999@s.whatsapp.net']], $result);
    }

    #[Test]
    public function it_can_find_chat()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/chat/findChats/test-instance', [
                'where' => ['remoteJid' => '5511999999999@s.whatsapp.net'],
            ])
            ->willReturn([]);

        (new Chat($service, 'test-instance'))->find('+55 (11) 99999-9999');
    }

    #[Test]
    public function it_can_find_group_chat_by_jid()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/chat/findChats/test-instance', [
                'where' => ['remoteJid' => '120363000000000000@g.us'],
            ])
            ->willReturn([]);

        (new Chat($service, 'test-instance'))->find('120363000000000000@g.us');
    }

    #[Test]
    public function it_can_get_chat_messages()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/chat/findMessages/test-instance', [
                'where' => ['key' => ['remoteJid' => '5511999999999@s.whatsapp.net']],
                'offset' => 20,
                'page' => 2,
            ])
            ->willReturn(['messages' => ['records' => []]]);

        $result = (new Chat($service, 'test-instance'))->messages('5511999999999', 20, 2);

        $this->assertArrayHasKey('messages', $result);
    }

    #[Test]
    public function it_can_delete_a_message_for_everyone()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('delete')
            ->with('/chat/deleteMessageForEveryone/test-instance', [], [
                'id' => 'ABC123',
                'remoteJid' => '120363000000000000@g.us',
                'fromMe' => true,
                'participant' => '5511999999999@s.whatsapp.net',
            ])
            ->willReturn(['status' => 'success']);

        (new Chat($service, 'test-instance'))->deleteMessageForEveryone(
            'ABC123',
            '120363000000000000@g.us',
            true,
            '5511999999999@s.whatsapp.net'
        );
    }

    #[Test]
    public function it_sends_bare_numbers_instead_of_legacy_c_us_jids()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/chat/fetchProfilePictureUrl/test-instance', ['number' => '5511999999999'])
            ->willReturn([]);

        (new Chat($service, 'test-instance'))->fetchProfilePictureUrl('+55 11 99999-9999');
    }

    #[Test]
    public function it_can_mark_chat_as_read()
    {
        $result = $this->chatResource->markAsRead([
            [
                'remoteJid' => '5511999999999@s.whatsapp.net',
                'fromMe' => false,
                'id' => 'ABC123',
            ],
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_mark_chat_unread()
    {
        $result = $this->chatResource->markChatUnread('5511999999999@s.whatsapp.net', [
            [
                'remoteJid' => '5511999999999@s.whatsapp.net',
                'fromMe' => false,
                'id' => 'ABC123',
            ],
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_archive_chat()
    {
        $result = $this->chatResource->archive('5511999999999@s.whatsapp.net', [
            'remoteJid' => '5511999999999@s.whatsapp.net',
            'fromMe' => false,
            'id' => 'ABC123',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_unarchive_chat()
    {
        $result = $this->chatResource->unarchive('5511999999999@s.whatsapp.net', [
            'remoteJid' => '5511999999999@s.whatsapp.net',
            'fromMe' => false,
            'id' => 'ABC123',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_message()
    {
        $result = $this->chatResource->updateMessage('5511999999999', 'Edited text', [
            'remoteJid' => '5511999999999@s.whatsapp.net',
            'fromMe' => true,
            'id' => 'ABC123',
        ]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_fetch_profile_picture_url()
    {
        $result = $this->chatResource->fetchProfilePictureUrl('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_find_status_message()
    {
        $result = $this->chatResource->findStatusMessage(['id' => 'ABC123'], 10);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_get_base64_from_media_message()
    {
        $result = $this->chatResource->getBase64FromMediaMessage('ABC123', true);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_update_block_status()
    {
        $result = $this->chatResource->updateBlockStatus('5511999999999', 'block');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_validates_block_status_value()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->chatResource->updateBlockStatus('5511999999999', 'invalid');
    }

    #[Test]
    public function it_can_start_typing()
    {
        $result = $this->chatResource->startTyping('5511999999999', 2000);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_stop_typing()
    {
        $result = $this->chatResource->stopTyping('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_find_chats()
    {
        $result = $this->chatResource->findChats(['where' => ['id' => 'foo']]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_find_messages()
    {
        $result = $this->chatResource->findMessages(['where' => ['id' => 'foo']]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_find_contacts()
    {
        $result = $this->chatResource->findContacts(['where' => ['id' => 'foo']]);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_verify_whatsapp_numbers()
    {
        $result = $this->chatResource->whatsappNumbers(['5511999999999']);

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
            'chats' => [
                [
                    'id' => '5511999999999@s.whatsapp.net',
                    'name' => 'Contact Name',
                    'unreadCount' => 0,
                ],
            ],
            'messages' => [
                [
                    'key' => [
                        'remoteJid' => '5511999999999@s.whatsapp.net',
                        'fromMe' => true,
                        'id' => '12345',
                    ],
                    'message' => [
                        'conversation' => 'Hello',
                    ],
                    'timestamp' => 1678901234,
                ],
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status' => 'success',
            'message' => 'Operation successful',
        ]);

        $this->chatResource = new Chat($this->service, 'test-instance');
    }
}
