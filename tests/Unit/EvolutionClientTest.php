<?php

// tests/Unit/EvolutionClientTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Happones\LaravelEvolutionClient\EvolutionApiClient;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Happones\LaravelEvolutionClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EvolutionClientTest extends TestCase
{
    /**
     * @var MockHandler
     */
    protected $mockHandler;

    /**
     * @var EvolutionApiClient
     */
    protected $client;

    #[Test]
    public function it_can_be_instantiated()
    {
        $this->assertInstanceOf(EvolutionApiClient::class, $this->client);
    }

    #[Test]
    public function it_can_set_instance_name()
    {
        $client = $this->client->instance('new-instance');

        $this->assertEquals('new-instance', $client->getInstanceName());
        $this->assertEquals('new-instance', $client->instance->getInstanceName());
    }

    #[Test]
    public function it_does_not_mutate_the_shared_client_when_switching_instances()
    {
        $sales = $this->client->instance('sales');
        $support = $this->client->instance('support');

        $this->assertNotSame($this->client, $sales);
        $this->assertEquals('default', $this->client->getInstanceName());
        $this->assertEquals('default', $this->client->message->getInstanceName());
        $this->assertEquals('sales', $sales->message->getInstanceName());
        $this->assertEquals('support', $support->message->getInstanceName());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockHandler = new MockHandler([
            new Response(200, [], json_encode([
                'status' => 'success',
                'message' => 'Mock response',
            ])),
        ]);

        $handlerStack = HandlerStack::create($this->mockHandler);
        $httpClient = new Client(['handler' => $handlerStack]);

        // Mock the EvolutionService but allow actual method calls
        $service = $this->getMockBuilder(EvolutionService::class)
            ->setConstructorArgs(['http://localhost:8080', 'test-api-key', 30])
            ->onlyMethods(['getClient'])
            ->getMock();

        $service->method('getClient')->willReturn($httpClient);

        $this->client = new EvolutionApiClient($service, 'default');
    }
}
