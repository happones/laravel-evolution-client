<?php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Webhook;

use Happones\LaravelEvolutionClient\Events\EvolutionWebhookReceived;
use Happones\LaravelEvolutionClient\Webhook\WebhookEvent;
use Happones\LaravelEvolutionClient\Webhook\WebhookProcessor;
use Illuminate\Events\Dispatcher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WebhookProcessorTest extends TestCase
{
    #[Test]
    public function it_runs_any_and_per_event_callbacks()
    {
        $seen = [];
        $processor = (new WebhookProcessor)
            ->onAny(function (WebhookEvent $e) use (&$seen) {
                $seen[] = 'any:' . $e->name();
            })
            ->on('messages.upsert', function (WebhookEvent $e) use (&$seen) {
                $seen[] = 'specific:' . $e->get('key.id');
            })
            ->on('connection.update', function () use (&$seen) {
                $seen[] = 'should-not-fire';
            });

        $processor->process([
            'event' => 'MESSAGES_UPSERT',
            'data' => ['key' => ['id' => 'XYZ']],
        ]);

        $this->assertSame(['any:messages.upsert', 'specific:XYZ'], $seen);
    }

    #[Test]
    public function it_dispatches_laravel_events_when_a_dispatcher_is_present()
    {
        $dispatcher = new Dispatcher;

        $received = null;
        $dispatcher->listen(EvolutionWebhookReceived::class, function (EvolutionWebhookReceived $e) use (&$received) {
            $received = $e->webhook->name();
        });

        $named = null;
        $dispatcher->listen('evolution.webhook.messages.upsert', function (WebhookEvent $e) use (&$named) {
            $named = $e->name();
        });

        $processor = new WebhookProcessor($dispatcher);
        $event = $processor->process(['event' => 'messages.upsert', 'data' => []]);

        $this->assertSame('messages.upsert', $received);
        $this->assertSame('messages.upsert', $named);
        $this->assertInstanceOf(WebhookEvent::class, $event);
    }

    #[Test]
    public function it_works_without_a_dispatcher()
    {
        $event = (new WebhookProcessor)->process(['event' => 'call', 'data' => []]);

        $this->assertSame('call', $event->name());
    }
}
