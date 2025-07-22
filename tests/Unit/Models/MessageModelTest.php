<?php
// tests/Unit/Models/MessageModelTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Models;

use PHPUnit\Framework\TestCase;
use Happones\LaravelEvolutionClient\Models\Contact;
use Happones\LaravelEvolutionClient\Models\ContactMessage;
use Happones\LaravelEvolutionClient\Models\ListMessage;
use Happones\LaravelEvolutionClient\Models\ListRow;
use Happones\LaravelEvolutionClient\Models\ListSection;
use Happones\LaravelEvolutionClient\Models\LocationMessage;
use Happones\LaravelEvolutionClient\Models\PollMessage;
use Happones\LaravelEvolutionClient\Models\QuotedMessage;
use Happones\LaravelEvolutionClient\Models\ReactionMessage;
use Happones\LaravelEvolutionClient\Models\StatusMessage;
use Happones\LaravelEvolutionClient\Models\TemplateMessage;
use Happones\LaravelEvolutionClient\Models\TextMessage;

class MessageModelTest extends TestCase
{
    /** @test */
    public function it_can_create_text_message()
    {
        $number = '5511999999999';
        $text   = 'Test message';
        $delay  = 1000;

        $message = new TextMessage($number, $text, $delay);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($text, $data['text']);
        $this->assertEquals($delay, $data['delay']);
    }

    /** @test */
    public function it_can_create_text_message_with_optional_parameters()
    {
        $number           = '5511999999999';
        $text             = 'Test message with link https://example.com';
        $delay            = 1000;
        $linkPreview      = true;
        $mentionsEveryOne = true;
        $mentioned        = ['5511888888888@c.us'];

        $message = new TextMessage($number, $text, $delay, null, $linkPreview, $mentionsEveryOne, $mentioned);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($text, $data['text']);
        $this->assertEquals($delay, $data['delay']);
        $this->assertTrue($data['linkPreview']);
        $this->assertTrue($data['mentionsEveryOne']);
        $this->assertEquals($mentioned, $data['mentioned']);
    }

    /** @test */
    public function it_can_create_text_message_with_quoted_message()
    {
        $number = '5511999999999';
        $text   = 'Reply to message';

        $quotedMessageKey = [
            'remoteJid' => '5511999999999@c.us',
            'fromMe'    => false,
            'id'        => '12345',
        ];

        $quoted  = new QuotedMessage($quotedMessageKey);
        $message = new TextMessage($number, $text, null, $quoted);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($text, $data['text']);
        $this->assertEquals($quotedMessageKey, $data['quoted']['key']);
    }

    /** @test */
    public function it_can_create_location_message()
    {
        $number    = '5511999999999';
        $name      = 'Test Location';
        $address   = 'Test Address, 123';
        $latitude  = -23.5505;
        $longitude = -46.6333;

        $message = new LocationMessage($number, $name, $address, $latitude, $longitude);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($name, $data['name']);
        $this->assertEquals($address, $data['address']);
        $this->assertEquals($latitude, $data['latitude']);
        $this->assertEquals($longitude, $data['longitude']);
    }

    /** @test */
    public function it_can_create_contact_message()
    {
        $number      = '5511999999999';
        $fullName    = 'Test Contact';
        $wuid        = '5511888888888';
        $phoneNumber = '5511888888888';

        $contact = new Contact($fullName, $wuid, $phoneNumber);
        $message = new ContactMessage($number, [$contact]);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertCount(1, $data['contact']);
        $this->assertEquals($fullName, $data['contact'][0]['fullName']);
        $this->assertEquals($wuid, $data['contact'][0]['wuid']);
        $this->assertEquals($phoneNumber, $data['contact'][0]['phoneNumber']);
    }

    /** @test */
    public function it_can_create_reaction_message()
    {
        $key = [
            'remoteJid' => '5511999999999@c.us',
            'fromMe'    => false,
            'id'        => '12345',
        ];
        $reaction = '👍';

        $message = new ReactionMessage($key, $reaction);
        $data    = $message->toArray();

        $this->assertEquals($key, $data['key']);
        $this->assertEquals($reaction, $data['reaction']);
    }

    /** @test */
    public function it_can_create_poll_message()
    {
        $number          = '5511999999999';
        $name            = 'Favorite Color?';
        $selectableCount = 1;
        $values          = ['Red', 'Green', 'Blue', 'Yellow'];

        $message = new PollMessage($number, $name, $selectableCount, $values);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($name, $data['name']);
        $this->assertEquals($selectableCount, $data['selectableCount']);
        $this->assertEquals($values, $data['values']);
    }

    /** @test */
    public function it_can_create_list_message()
    {
        $number      = '5511999999999';
        $title       = 'Test List';
        $description = 'Choose an option';
        $buttonText  = 'View Options';
        $footerText  = 'Footer text';

        $rows1 = [
            new ListRow('Option 1', 'Description 1', 'opt1'),
            new ListRow('Option 2', 'Description 2', 'opt2'),
        ];

        $rows2 = [
            new ListRow('Option 3', 'Description 3', 'opt3'),
            new ListRow('Option 4', 'Description 4', 'opt4'),
        ];

        $sections = [
            new ListSection('Section 1', $rows1),
            new ListSection('Section 2', $rows2),
        ];

        $message = new ListMessage($number, $title, $description, $buttonText, $footerText, $sections);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($title, $data['title']);
        $this->assertEquals($description, $data['description']);
        $this->assertEquals($buttonText, $data['buttonText']);
        $this->assertEquals($footerText, $data['footerText']);
        $this->assertCount(2, $data['sections']);
        $this->assertEquals('Section 1', $data['sections'][0]['title']);
        $this->assertEquals('Section 2', $data['sections'][1]['title']);
        $this->assertCount(2, $data['sections'][0]['rows']);
        $this->assertCount(2, $data['sections'][1]['rows']);
    }

    /** @test */
    public function it_can_create_status_message()
    {
        $type            = 'text';
        $content         = 'Status update test';
        $backgroundColor = '#25D366';
        $font            = 2;
        $allContacts     = true;

        $message = new StatusMessage($type, $content, null, $backgroundColor, $font, $allContacts);
        $data    = $message->toArray();

        $this->assertEquals($type, $data['type']);
        $this->assertEquals($content, $data['content']);
        $this->assertEquals($backgroundColor, $data['backgroundColor']);
        $this->assertEquals($font, $data['font']);
        $this->assertTrue($data['allContacts']);
    }

    /** @test */
    public function it_can_create_template_message()
    {
        $number     = '5511999999999';
        $name       = 'hello_world';
        $language   = 'en_US';
        $components = [
            [
                'type'       => 'body',
                'parameters' => [
                    [
                        'type' => 'text',
                        'text' => 'John Doe',
                    ],
                ],
            ],
        ];

        $message = new TemplateMessage($number, $name, $language, $components);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($name, $data['name']);
        $this->assertEquals($language, $data['language']);
        $this->assertEquals($components, $data['components']);
    }

    /** @test */
    public function it_can_create_template_message_with_webhook_url()
    {
        $number     = '5511999999999';
        $name       = 'hello_world';
        $language   = 'en_US';
        $components = [];
        $webhookUrl = 'https://example.com/webhook';

        $message = new TemplateMessage($number, $name, $language, $components, $webhookUrl);
        $data    = $message->toArray();

        $this->assertEquals($number, $data['number']);
        $this->assertEquals($name, $data['name']);
        $this->assertEquals($language, $data['language']);
        $this->assertEquals($components, $data['components']);
        $this->assertEquals($webhookUrl, $data['webhookUrl']);
    }
}
