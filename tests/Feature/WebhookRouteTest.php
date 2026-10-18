<?php

namespace Happones\LaravelEvolutionClient\Tests\Feature;

use Happones\LaravelEvolutionClient\Events\EvolutionWebhookReceived;
use Happones\LaravelEvolutionClient\Jobs\ProcessEvolutionWebhook;
use Happones\LaravelEvolutionClient\Tests\TestCase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;

class WebhookRouteTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('evolution.webhook.route.enabled', true);
        $app['config']->set('evolution.webhook.route.path', 'evolution/webhook');
        $app['config']->set('evolution.webhook.route.middleware', []);
    }

    #[Test]
    public function it_receives_a_webhook_and_dispatches_events_inline()
    {
        Event::fake([EvolutionWebhookReceived::class]);

        $response = $this->postJson('evolution/webhook', [
            'event' => 'messages.upsert',
            'instance' => 'sales',
            'data' => ['key' => ['id' => 'ABC']],
        ]);

        $response->assertOk()->assertJson([
            'status' => 'received',
            'event' => 'messages.upsert',
        ]);

        Event::assertDispatched(
            EvolutionWebhookReceived::class,
            fn (EvolutionWebhookReceived $e) => $e->webhook->name() === 'messages.upsert'
                && $e->webhook->instance === 'sales'
        );
    }

    #[Test]
    public function it_queues_processing_when_the_queue_is_enabled()
    {
        config()->set('evolution.webhook.queue.enabled', true);
        Bus::fake();

        $response = $this->postJson('evolution/webhook', [
            'event' => 'messages.upsert',
            'data' => [],
        ]);

        $response->assertOk()->assertJson(['status' => 'queued']);

        Bus::assertDispatched(ProcessEvolutionWebhook::class);
    }

    #[Test]
    public function it_rejects_payloads_without_an_event_name()
    {
        Event::fake([EvolutionWebhookReceived::class]);

        $this->postJson('evolution/webhook', ['data' => []])->assertStatus(422);

        Event::assertNotDispatched(EvolutionWebhookReceived::class);
    }

    #[Test]
    public function it_rejects_requests_without_the_configured_secret()
    {
        config()->set('evolution.webhook.secret', 's3cret');
        Event::fake([EvolutionWebhookReceived::class]);

        $this->postJson('evolution/webhook', ['event' => 'messages.upsert'])->assertUnauthorized();
        $this->postJson('evolution/webhook', ['event' => 'messages.upsert'], ['X-Evolution-Secret' => 'wrong'])
            ->assertUnauthorized();

        Event::assertNotDispatched(EvolutionWebhookReceived::class);
    }

    #[Test]
    public function it_accepts_requests_with_the_configured_secret()
    {
        config()->set('evolution.webhook.secret', 's3cret');
        config()->set('evolution.webhook.secret_header', 'X-Webhook-Token');
        Event::fake([EvolutionWebhookReceived::class]);

        $this->postJson('evolution/webhook', ['event' => 'messages.upsert'], ['X-Webhook-Token' => 's3cret'])
            ->assertOk()
            ->assertJson(['status' => 'received']);

        Event::assertDispatched(EvolutionWebhookReceived::class);
    }
}
