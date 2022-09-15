<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Config;

use Src\Shared\Domain\Config\Config;

final class LaravelConfig implements Config
{
    public function get(string $key, mixed $default = null): mixed
    {
        return config($key, $default);
    }
}
