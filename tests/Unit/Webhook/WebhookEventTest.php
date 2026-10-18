<?php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Webhook;

use Happones\LaravelEvolutionClient\Webhook\WebhookEvent;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WebhookEventTest extends TestCase
{
    #[Test]
    public function it_parses_a_raw_payload()
    {
        $event = WebhookEvent::fromArray([
            'event' => 'messages.upsert',
            'instance' => 'sales',
            'data' => ['key' => ['id' => 'ABC'], 'message' => ['conversation' => 'hi']],
            'sender' => '5511999999999@s.whatsapp.net',
            'server_url' => 'http://localhost:8080',
            'date_time' => '2026-07-08T00:00:00.000Z',
            'destination' => 'http://app.test/webhook',
        ]);

        $this->assertSame('messages.upsert', $event->name());
        $this->assertSame('sales', $event->instance);
        $this->assertSame('ABC', $event->get('key.id'));
        $this->assertSame('hi', $event->get('message.conversation'));
        $this->assertSame('5511999999999@s.whatsapp.net', $event->sender);
        $this->assertTrue($event->isMessageUpsert());
    }

    #[Test]
    public function it_normalizes_event_names_to_lowercase_dotted()
    {
        $this->assertSame('messages.upsert', WebhookEvent::normalizeEventName('MESSAGES_UPSERT'));
        $this->assertSame('connection.update', WebhookEvent::normalizeEventName('CONNECTION_UPDATE'));

        $event = WebhookEvent::fromArray(['event' => 'CONNECTION_UPDATE', 'data' => []]);
        $this->assertTrue($event->is('connection.update'));
        $this->assertTrue($event->is('CONNECTION_UPDATE'));
        $this->assertTrue($event->isConnectionUpdate());
    }

    #[Test]
    public function it_defaults_missing_fields_gracefully()
    {
        $event = WebhookEvent::fromArray([]);

        $this->assertSame('unknown', $event->name());
        $this->assertNull($event->instance);
        $this->assertSame([], $event->data);
        $this->assertSame('fallback', $event->get('missing', 'fallback'));
    }

    #[Test]
    public function it_exposes_the_raw_payload()
    {
        $payload = ['event' => 'call', 'data' => ['id' => 1]];

        $this->assertSame($payload, WebhookEvent::fromArray($payload)->toArray());
    }
}
