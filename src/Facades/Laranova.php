<?php

declare(strict_types=1);

namespace Laranova\Facades;

use Illuminate\Support\Facades\Facade;
use Laranova\Services\ApiClient;

/**
 * @method static array send(string $method, string $url, array $headers = [], ?string $body = null, ?string $preScripts = null)
 *
 * @see \Laranova\Services\ApiClient
 */
final class Laranova extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ApiClient::class;
    }
}
