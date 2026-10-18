<?php

// tests/Unit/Resources/MessageResourceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Happones\LaravelEvolutionClient\Models\Button;
use Happones\LaravelEvolutionClient\Models\ListRow;
use Happones\LaravelEvolutionClient\Models\ListSection;
use Happones\LaravelEvolutionClient\Resources\Message;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MessageResourceTest extends TestCase
{
    /**
     * @var Message
     */
    protected $messageResource;

    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var EvolutionService
     */
    protected $service;

    #[Test]
    public function it_can_send_text_message()
    {
        $result = $this->messageResource->sendText('5511999999999', 'Test message');

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertArrayHasKey('key', $result);
    }

    #[Test]
    public function it_validates_phone_number_when_sending_text()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->messageResource->sendText('', 'Test message');
    }

    #[Test]
    public function it_validates_message_text_when_sending_text()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->messageResource->sendText('5511999999999', '');
    }

    #[Test]
    public function it_sends_images_through_the_v2_send_media_endpoint()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/message/sendMedia/test-instance', [
                'number' => '5511999999999',
                'mediatype' => 'image',
                'mimetype' => 'image/jpeg',
                'caption' => 'Look',
                'media' => 'https://example.com/a.jpg',
                'fileName' => 'image.jpg',
            ])
            ->willReturn(['status' => 'success']);

        (new Message($service, 'test-instance'))->sendImage('5511999999999', 'https://example.com/a.jpg', 'Look');
    }

    #[Test]
    public function it_sends_documents_through_the_v2_send_media_endpoint()
    {
        $service = $this->createMock(EvolutionService::class);
        $service->expects($this->once())
            ->method('post')
            ->with('/message/sendMedia/test-instance', [
                'number' => '120363000000000000@g.us',
                'mediatype' => 'document',
                'mimetype' => 'application/pdf',
                'caption' => '',
                'media' => 'https://example.com/invoice.pdf',
                'fileName' => 'invoice.pdf',
            ])
            ->willReturn(['status' => 'success']);

        (new Message($service, 'test-instance'))
            ->sendDocument('120363000000000000', 'https://example.com/invoice.pdf', 'invoice.pdf', isGroup: true);
    }

    #[Test]
    public function it_formats_phone_number_correctly()
    {
        // Create a subclass that makes the protected method public for testing
        $messageResource = new class($this->service, 'test-instance') extends Message
        {
            public function publicFormatPhoneNumber(string $phoneNumber): string
            {
                return $this->formatPhoneNumber($phoneNumber);
            }
        };

        // Evolution v2 expects the bare number; it builds the JID server-side
        $this->assertEquals('5511999999999', $messageResource->publicFormatPhoneNumber('5511999999999'));
        $this->assertEquals('5511999999999', $messageResource->publicFormatPhoneNumber('+55 (11) 99999-9999'));

        // Existing JIDs are passed through unchanged
        $this->assertEquals('5511999999999@s.whatsapp.net', $messageResource->publicFormatPhoneNumber('5511999999999@s.whatsapp.net'));
        $this->assertEquals('123456789@lid', $messageResource->publicFormatPhoneNumber('123456789@lid'));
    }

    #[Test]
    public function it_can_send_location_message()
    {
        $result = $this->messageResource->sendLocation(
            '5511999999999',
            -23.5505,
            -46.6333,
            'São Paulo',
            'Paulista Avenue, 1000'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_media_message()
    {
        $result = $this->messageResource->sendMedia(
            '5511999999999',
            'image',
            'image/png',
            'My caption',
            'https://example.com/image.png',
            'image.png'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_contact_message()
    {
        $result = $this->messageResource->sendContact(
            '5511999999999',
            'Contact Name',
            '5511888888888'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_poll_message()
    {
        $result = $this->messageResource->sendPoll(
            '5511999999999',
            'Favorite Color?',
            1,
            ['Red', 'Green', 'Blue', 'Yellow']
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_list_message()
    {
        $rows1 = [
            new ListRow('Option 1', 'Description 1', 'opt1'),
            new ListRow('Option 2', 'Description 2', 'opt2'),
        ];

        $sections = [
            new ListSection('Section 1', $rows1),
        ];

        $result = $this->messageResource->sendList(
            '5511999999999',
            'Test List',
            'Choose an option',
            'View Options',
            'Footer text',
            $sections
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_buttons_message()
    {
        $buttons = [
            new Button('reply', 'Yes', ['id' => 'btn-yes']),
            new Button('reply', 'No', ['id' => 'btn-no']),
        ];

        $result = $this->messageResource->sendButtons(
            '5511999999999',
            'Confirmation',
            'Do you want to proceed?',
            'Choose an option',
            $buttons
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_audio_message()
    {
        $result = $this->messageResource->sendAudio(
            '5511999999999',
            'https://example.com/audio.mp3'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_sticker_message()
    {
        $result = $this->messageResource->sendSticker(
            '5511999999999',
            'https://example.com/sticker.webp'
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    #[Test]
    public function it_can_send_template_message()
    {
        $result = $this->messageResource->sendTemplate(
            '5511999999999',
            'hello_world',
            'en_US',
            [
                [
                    'type' => 'body',
                    'parameters' => [
                        [
                            'type' => 'text',
                            'text' => 'John Doe',
                        ],
                    ],
                ],
            ]
        );

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'key' => [
                    'id' => '123456',
                    'remoteJid' => '5511999999999@s.whatsapp.net',
                    'fromMe' => true,
                ],
            ])),
        ]);

        $handlerStack = HandlerStack::create($this->mockHandler);
        $client = new Client(['handler' => $handlerStack]);

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('post')->willReturn([
            'status' => 'success',
            'key' => [
                'id' => '123456',
                'remoteJid' => '5511999999999@s.whatsapp.net',
                'fromMe' => true,
            ],
        ]);

        $this->messageResource = new Message($this->service, 'test-instance');
    }
}
