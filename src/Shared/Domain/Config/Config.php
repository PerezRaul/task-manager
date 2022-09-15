<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Config;

interface Config
{
    public function get(string $key, mixed $default = null): mixed;
}
