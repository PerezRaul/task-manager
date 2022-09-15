<?php

declare(strict_types=1);

namespace Src\Shared\Application;

use Src\Shared\Domain\Bus\Query\Response;

final class StringResponse implements Response
{
    public function __construct(private string $value)
    {
    }

    public function value(): string
    {
        return $this->value;
    }
}
