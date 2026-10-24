<?php

// tests/Unit/Services/EvolutionServiceTest.php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Happones\LaravelEvolutionClient\Exceptions\EvolutionApiException;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Happones\LaravelEvolutionClient\Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class EvolutionServiceTest extends TestCase
{
    #[Test]
    public function it_treats_error_false_as_a_success()
    {
        $response = $this->serviceAnswering(['status' => 'SUCCESS', 'error' => false, 'response' => ['message' => 'Instance logged out']])
            ->delete('instance/logout/sales');

        $this->assertFalse($response['error']);
        $this->assertSame('Instance logged out', $response['response']['message']);
    }

    #[Test]
    public function it_throws_the_api_error_without_wrapping_it()
    {
        try {
            $this->serviceAnswering(['error' => 'Instance not found', 'code' => 404])->get('instance/fetchInstances');
            $this->fail('Expected an EvolutionApiException.');
        } catch (EvolutionApiException $e) {
            $this->assertSame('Instance not found', $e->getMessage());
            $this->assertSame(404, $e->getCode());
        }
    }

    #[Test]
    public function it_reads_the_message_of_an_error_status()
    {
        $this->expectException(EvolutionApiException::class);
        $this->expectExceptionMessage('Something failed');
        $this->expectExceptionCode(400);

        $this->serviceAnswering(['status' => 'error', 'message' => 'Something failed'])->get('instance/fetchInstances');
    }

    #[Test]
    public function it_reports_invalid_json_as_is()
    {
        try {
            $this->serviceAnswering('<html>')->get('instance/fetchInstances');
            $this->fail('Expected an EvolutionApiException.');
        } catch (EvolutionApiException $e) {
            $this->assertSame('Invalid JSON response from API', $e->getMessage());
            $this->assertSame(500, $e->getCode());
        }
    }

    /**
     * @param array<string, mixed>|string $body
     */
    protected function serviceAnswering(array|string $body): EvolutionService
    {
        $client = new Client(['handler' => HandlerStack::create(new MockHandler([
            new Response(200, [], is_string($body) ? $body : json_encode($body)),
        ]))]);

        return new class('http://localhost:8080', 'test-api-key', $client) extends EvolutionService
        {
            public function __construct(string $baseUrl, string $apiKey, Client $client)
            {
                parent::__construct($baseUrl, $apiKey);
                $this->client = $client;
            }
        };
    }
}
