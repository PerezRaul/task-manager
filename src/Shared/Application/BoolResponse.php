<?php

declare(strict_types=1);

namespace Src\Shared\Application;

use Src\Shared\Domain\Bus\Query\Response;

final class BoolResponse implements Response
{
    public function __construct(private bool $bool)
    {
    }

    public function isTrue(): bool
    {
        return $this->bool;
    }

    public function isFalse(): bool
    {
        return !$this->isTrue();
    }
}
