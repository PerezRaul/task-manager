<?php

declare(strict_types=1);

namespace Src\Shared\Application;

use Src\Shared\Domain\Bus\Query\Response;

final class CounterResponse implements Response
{
    public function __construct(private int $total)
    {
    }

    public function total(): int
    {
        return $this->total;
    }
}
