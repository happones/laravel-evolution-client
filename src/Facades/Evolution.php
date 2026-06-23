<?php
// src/Facades/Evolution.php

namespace Happones\LaravelEvolutionClient\Facades;

use Happones\LaravelEvolutionClient\EvolutionApiClient;
use Happones\LaravelEvolutionClient\Resources\Business;
use Happones\LaravelEvolutionClient\Resources\Call;
use Happones\LaravelEvolutionClient\Resources\Chat;
use Happones\LaravelEvolutionClient\Resources\EvolutionBot;
use Happones\LaravelEvolutionClient\Resources\Group;
use Happones\LaravelEvolutionClient\Resources\Instance;
use Happones\LaravelEvolutionClient\Resources\Label;
use Happones\LaravelEvolutionClient\Resources\Message;
use Happones\LaravelEvolutionClient\Resources\OpenAIBot;
use Happones\LaravelEvolutionClient\Resources\Profile;
use Happones\LaravelEvolutionClient\Resources\Proxy;
use Happones\LaravelEvolutionClient\Resources\Settings;
use Happones\LaravelEvolutionClient\Resources\Template;
use Happones\LaravelEvolutionClient\Resources\WebSocket;
use Illuminate\Support\Facades\Facade;

/**
 * @method static EvolutionApiClient instance(string $instanceName)
 * @method static array createInstance(string $instanceName)
 * @method static array getQrCode()
 * @method static bool isConnected()
 * @method static array disconnect()
 * @method static array sendText(string $phoneNumber, string $message)
 * @method static Chat getChatAttribute()
 * @method static Group getGroupAttribute()
 * @method static Message getMessageAttribute()
 * @method static Instance getInstanceAttribute()
 * @method static Call getCallAttribute()
 * @method static Label getLabelAttribute()
 * @method static Profile getProfileAttribute()
 * @method static WebSocket getWebsocketAttribute()
 * @method static EvolutionBot getEvolutionBotAttribute()
 * @method static OpenAIBot getOpenAIBotAttribute()
 * @method static Template getTemplateAttribute()
 * @method static Proxy getProxyAttribute()
 * @method static Settings getSettingsAttribute()
 * @method static Business getBusinessAttribute()
 *
 * @see EvolutionApiClient
 */
class Evolution extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'evolution';
    }
}
