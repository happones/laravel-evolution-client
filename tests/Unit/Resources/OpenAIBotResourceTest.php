<?php

namespace Happones\LaravelEvolutionClient\Tests\Unit\Resources;

use Happones\LaravelEvolutionClient\Resources\OpenAIBot;
use Happones\LaravelEvolutionClient\Services\EvolutionService;
use Happones\LaravelEvolutionClient\Tests\TestCase;
use InvalidArgumentException;
use Mockery;

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
            ->with('/openai/create/default', [
                'enabled' => true,
                'openaiCredsId' => 'creds-1',
                'botType' => 'chatCompletion',
                'model' => 'gpt-4o',
                'triggerType' => 'all',
            ])
            ->andReturn(['id' => 'bot-123']);

        $response = $this->openAIBot->create('creds-1', 'gpt-4o', options: ['triggerType' => 'all']);

        $this->assertEquals(['id' => 'bot-123'], $response);
    }

    public function test_it_rejects_an_unknown_bot_type()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->openAIBot->create('creds-1', 'gpt-4o', 'unknown');
    }

    public function test_it_can_find_a_bot()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('/openai/find/bot-123/default')
            ->andReturn(['id' => 'bot-123']);

        $this->assertEquals(['id' => 'bot-123'], $this->openAIBot->find('bot-123'));
    }

    public function test_it_can_find_all_bots()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('/openai/find/default')
            ->andReturn([['id' => 'bot-123'], ['id' => 'bot-456']]);

        $this->assertEquals([['id' => 'bot-123'], ['id' => 'bot-456']], $this->openAIBot->findAll());
    }

    public function test_it_can_update_a_bot()
    {
        $this->mockService->shouldReceive('put')
            ->once()
            ->with('/openai/update/bot-123/default', ['model' => 'gpt-4o-mini'])
            ->andReturn(['success' => true]);

        $this->assertEquals(['success' => true], $this->openAIBot->update('bot-123', ['model' => 'gpt-4o-mini']));
    }

    public function test_it_can_delete_a_bot()
    {
        $this->mockService->shouldReceive('delete')
            ->once()
            ->with('/openai/delete/bot-123/default')
            ->andReturn(['success' => true]);

        $this->assertEquals(['success' => true], $this->openAIBot->delete('bot-123'));
    }

    public function test_it_can_get_credentials()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('/openai/creds/default')
            ->andReturn([['id' => 'creds-1']]);

        $this->assertEquals([['id' => 'creds-1']], $this->openAIBot->getCredentials());
    }

    public function test_it_can_set_credentials()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('/openai/creds/default', ['name' => 'main', 'apiKey' => 'sk-new-key'])
            ->andReturn(['id' => 'creds-1']);

        $this->assertEquals(['id' => 'creds-1'], $this->openAIBot->setCredentials('main', 'sk-new-key'));
    }

    public function test_it_can_delete_credentials()
    {
        $this->mockService->shouldReceive('delete')
            ->once()
            ->with('/openai/creds/creds-1/default')
            ->andReturn(['success' => true]);

        $this->assertEquals(['success' => true], $this->openAIBot->deleteCredentials('creds-1'));
    }

    public function test_it_can_update_settings()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('/openai/settings/default', ['expire' => 20])
            ->andReturn(['success' => true]);

        $this->assertEquals(['success' => true], $this->openAIBot->updateSettings(['expire' => 20]));
    }

    public function test_it_can_get_settings()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('/openai/fetchSettings/default')
            ->andReturn(['expire' => 20]);

        $this->assertEquals(['expire' => 20], $this->openAIBot->getSettings());
    }

    public function test_it_can_change_status()
    {
        $this->mockService->shouldReceive('post')
            ->once()
            ->with('/openai/changeStatus/default', [
                'remoteJid' => '5511999999999@s.whatsapp.net',
                'status' => 'paused',
            ])
            ->andReturn(['success' => true]);

        $this->assertEquals(
            ['success' => true],
            $this->openAIBot->changeStatus('5511999999999@s.whatsapp.net', 'paused')
        );
    }

    public function test_it_rejects_an_unknown_session_status()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->openAIBot->changeStatus('5511999999999@s.whatsapp.net', 'active');
    }

    public function test_it_can_find_session()
    {
        $this->mockService->shouldReceive('get')
            ->once()
            ->with('/openai/fetchSessions/bot-123/default')
            ->andReturn([['id' => 'session-1']]);

        $this->assertEquals([['id' => 'session-1']], $this->openAIBot->findSession('bot-123'));
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
