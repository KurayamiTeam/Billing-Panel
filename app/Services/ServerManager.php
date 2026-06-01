<?php

namespace App\Services;

use InvalidArgumentException;
use App\Services\Drivers\PterodactylDriver;
use App\Services\Drivers\VirtualizorDriver;

class ServerManager
{
    public static function make(string $driver)
    {
        return match ($driver) {
            'pterodactyl' => new PterodactylDriver(),
            'virtualizor' => new VirtualizorDriver(),
            default => throw new InvalidArgumentException("Driver [{$driver}] not supported."),
        };
    }
}