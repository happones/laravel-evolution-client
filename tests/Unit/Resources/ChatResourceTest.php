<?php
// tests/Unit/Resources/ChatResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\Chat;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
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

    /** @test */
    public function it_can_get_all_chats()
    {
        $result = $this->chatResource->all();

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('chats', $result);
    }

    /** @test */
    public function it_can_find_chat()
    {
        $result = $this->chatResource->find('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_get_chat_messages()
    {
        $result = $this->chatResource->messages('5511999999999', 20);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('messages', $result);
    }

    /** @test */
    public function it_can_mark_chat_as_read()
    {
        $result = $this->chatResource->markAsRead('5511999999999');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_start_typing()
    {
        $result = $this->chatResource->startTyping('5511999999999', 2000);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    /** @test */
    public function it_can_stop_typing()
    {
        $result = $this->chatResource->stopTyping('5511999999999');

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
            'chats'  => [
                [
                    'id'          => '5511999999999@c.us',
                    'name'        => 'Contact Name',
                    'unreadCount' => 0,
                ],
            ],
            'messages' => [
                [
                    'key' => [
                        'remoteJid' => '5511999999999@c.us',
                        'fromMe'    => true,
                        'id'        => '12345',
                    ],
                    'message' => [
                        'conversation' => 'Hello',
                    ],
                    'timestamp' => 1678901234,
                ],
            ],
        ]);

        $this->service->method('post')->willReturn([
            'status'  => 'success',
            'message' => 'Operation successful',
        ]);

        $this->chatResource = new Chat($this->service, 'test-instance');
    }
}
