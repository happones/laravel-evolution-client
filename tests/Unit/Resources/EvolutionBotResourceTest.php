<?php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\EvolutionBot;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EvolutionBotResourceTest extends TestCase
{
    /**
     * @var EvolutionBot
     */
    protected EvolutionBot $evolutionBotResource;

    /**
     * @var EvolutionService|MockObject
     */
    protected EvolutionService|MockObject $service;

    /** @test */
    public function it_can_create_evolution_bot()
    {
        $payload = $this->getBotDataPayload();

        $result = $this->evolutionBotResource->create(...$payload);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals('bot_created_123', $result['bot']['id']);
    }

    /** @test */
    public function it_can_update_evolution_bot()
    {
        $evolutionBotId     = 'bot_to_update_456';
        $payload            = $this->getBotDataPayload();
        $payload['enabled'] = false; // A change for the update

        $result = $this->evolutionBotResource->update($evolutionBotId, ...$payload);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertEquals($evolutionBotId, $result['bot']['id']);
        $this->assertFalse($result['bot']['enabled']);
    }

    /** @test */
    public function it_can_delete_evolution_bot()
    {
        $evolutionBotId = 'bot_to_delete_789';

        $result = $this->evolutionBotResource->destroy($evolutionBotId);

        $this->assertIsArray($result);
        $this->assertEquals('success', $result['status']);
        $this->assertStringContainsString('deleted successfully', $result['message']);
    }

    /**
     * Set up the service mock and the class under test before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->getMockBuilder(EvolutionService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->service->method('post')->willReturn([
            'status' => 'success',
            'bot'    => ['id' => 'bot_created_123', 'enabled' => true],
        ]);

        $this->service->method('put')->willReturn([
            'status' => 'success',
            'bot'    => ['id' => 'bot_to_update_456', 'enabled' => false],
        ]);

        $this->service->method('delete')->willReturn([
            'status'  => 'success',
            'message' => 'Bot deleted successfully.',
        ]);

        $this->evolutionBotResource = new EvolutionBot($this->service, 'test-instance');
    }

    /**
     * Returns an array of sample data for creating/updating a bot.
     *
     * @return array
     */
    private function getBotDataPayload(): array
    {
        return [
            'enabled'         => true,
            'apiUrl'          => 'https://test.com/api',
            'apiKey'          => 'test-api-key',
            'triggerType'     => 'keyword',
            'triggerOperator' => 'equals',
            'triggerValue'    => '!test',
            'expire'          => 300,
            'keywordFinish'   => '!exit',
            'delayMessage'    => 1000,
            'unknownMessage'  => 'Unknown command',
            'listeningFromMe' => false,
            'stopBotFromMe'   => true,
            'keepOpen'        => false,
            'debounceTime'    => 500,
        ];
    }
}
