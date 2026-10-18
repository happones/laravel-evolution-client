<?php

// tests/Feature/EvolutionServiceProviderTest.php

namespace Happones\LaravelEvolutionClient\Tests\Feature;

use Happones\LaravelEvolutionClient\EvolutionApiClient;
use Happones\LaravelEvolutionClient\EvolutionServiceProvider;
use Happones\LaravelEvolutionClient\Facades\Evolution;
use Orchestra\Testbench\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EvolutionServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_the_service()
    {
        $this->assertTrue($this->app->bound('evolution'));
        $this->assertInstanceOf(EvolutionApiClient::class, $this->app->make('evolution'));
    }

    #[Test]
    public function switching_instances_through_the_facade_does_not_leak_into_the_singleton()
    {
        $default = Evolution::getInstanceName();

        $sales = Evolution::instance('sales');

        $this->assertEquals('sales', $sales->message->getInstanceName());
        $this->assertEquals($default, Evolution::getInstanceName());
        $this->assertEquals($default, app('evolution')->message->getInstanceName());
    }

    #[Test]
    public function it_loads_the_config()
    {
        $this->assertEquals('http://localhost:8080', config('evolution.base_url'));
        $this->assertEquals('test-api-key', config('evolution.api_key'));
        $this->assertEquals('test-instance', config('evolution.default_instance'));
    }

    #[Test]
    public function the_facade_works()
    {
        $this->assertInstanceOf(EvolutionApiClient::class, app('evolution'));
    }

    #[Test]
    public function the_facade_provides_access_to_all_resources()
    {
        $client = app('evolution');
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Chat', $client->chat);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Group', $client->group);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Message', $client->message);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Instance', $client->instance);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Call', $client->call);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Label', $client->label);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Profile', $client->profile);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\WebSocket', $client->websocket);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Proxy', $client->proxy);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Settings', $client->settings);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Template', $client->template);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\OpenAIBot', $client->openAIBot);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\EvolutionBot', $client->evolutionBot);
        $this->assertInstanceOf('Happones\LaravelEvolutionClient\Resources\Business', $client->business);
    }

    #[Test]
    public function it_publishes_the_config()
    {
        $this->artisan('vendor:publish', [
            '--provider' => 'Happones\LaravelEvolutionClient\EvolutionServiceProvider',
            '--tag' => 'evolution-config',
        ]);

        $this->assertFileExists(config_path('evolution.php'));
    }

    protected function getPackageProviders($app)
    {
        return [
            EvolutionServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Evolution' => Evolution::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Configurations for tests
        $app['config']->set('evolution.base_url', 'http://localhost:8080');
        $app['config']->set('evolution.api_key', 'test-api-key');
        $app['config']->set('evolution.default_instance', 'test-instance');
    }
}
