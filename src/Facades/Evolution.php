<?php
// src/Facades/Evolution.php

namespace Happones\LaravelEvolutionClient\Facades;

use Happones\LaravelEvolutionClient\EvolutionApiClient;
use Happones\LaravelEvolutionClient\Resources\Call;
use Happones\LaravelEvolutionClient\Resources\Chat;
use Happones\LaravelEvolutionClient\Resources\Group;
use Happones\LaravelEvolutionClient\Resources\Instance;
use Happones\LaravelEvolutionClient\Resources\Label;
use Happones\LaravelEvolutionClient\Resources\Message;
use Happones\LaravelEvolutionClient\Resources\Profile;
use Happones\LaravelEvolutionClient\Resources\WebSocket;
use Illuminate\Support\Facades\Facade;

/**
 * @method static EvolutionApiClient instance(string $instanceName)
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
    protected static function getFacadeAccessor()
    {
        return 'evolution';
    }
}
