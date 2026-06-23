<?php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\OpenAIBot;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Mockery;
use Happones\LaravelEvolutionClient\Tests\TestCase;

class OpenAIBotResourceTest extends TestCase
{
    protected $mockService;
    protected $openAIBot;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockService = Mockery::mock(EvolutionService::class);
        $this->openAIBot = new OpenAIBot($this->mockService, 'default');
    }

    public function test_it_can_create_a_bot()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('integrations/openai/bot', ['botName' => 'test-bot', 'model' => 'gpt-4', 'prompt' => 'You are a helpful assistant.'])
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->create('test-bot', 'gpt-4', ['prompt' => 'You are a helpful assistant.']);

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_find_a_bot()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('integrations/openai/bot/bot-123')
            ->andReturn(['id' => 'bot-123', 'name' => 'test-bot']);

        $response = $this->openAIBot->find('bot-123');

        $this->assertEquals(['id' => 'bot-123', 'name' => 'test-bot'], $response);
    }

    public function test_it_can_find_all_bots()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('integrations/openai/bot')
            ->andReturn([['id' => 'bot-123'], ['id' => 'bot-456']]);

        $response = $this->openAIBot->findAll();

        $this->assertEquals([['id' => 'bot-123'], ['id' => 'bot-456']], $response);
    }

    public function test_it_can_update_a_bot()
    {
        $this->mockService->shouldReceive('put')
            ->once()
            ->with('integrations/openai/bot/bot-123', ['prompt' => 'New prompt'])
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->update('bot-123', ['prompt' => 'New prompt']);

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_delete_a_bot()
    {
        $this->mockService->shouldReceive('delete')
            ->once()
            ->with('integrations/openai/bot/bot-123')
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->delete('bot-123');

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_get_credentials()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('integrations/openai/credentials')
            ->andReturn(['apiKey' => 'sk-123...']);

        $response = $this->openAIBot->getCredentials();

        $this->assertEquals(['apiKey' => 'sk-123...'], $response);
    }

    public function test_it_can_set_credentials()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('integrations/openai/credentials', ['apiKey' => 'sk-new-key'])
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->setCredentials('sk-new-key');

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_delete_credentials()
    {
        $this->mockService->shouldReceive('delete')
            ->once()
            ->with('integrations/openai/credentials')
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->deleteCredentials();

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_update_settings()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('integrations/openai/settings', ['temperature' => 0.8])
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->updateSettings(['temperature' => 0.8]);

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_get_settings()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('integrations/openai/settings')
            ->andReturn(['temperature' => 0.7]);

        $response = $this->openAIBot->getSettings();

        $this->assertEquals(['temperature' => 0.7], $response);
    }

    public function test_it_can_change_status()
    {
        $this->mockService->shouldReceive('put')
            ->once()
            ->with('integrations/openai/status', ['isActive' => true])
            ->andReturn(['success' => true]);

        $response = $this->openAIBot->changeStatus(true);

        $this->assertEquals(['success' => true], $response);
    }

    public function test_it_can_find_session()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('integrations/openai/session/bot-123')
            ->andReturn(['session' => 'active']);

        $response = $this->openAIBot->findSession('bot-123');

        $this->assertEquals(['session' => 'active'], $response);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}