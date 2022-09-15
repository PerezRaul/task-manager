<?php

declare(strict_types=1);

namespace Src\Shared\Application;

use Src\Shared\Domain\Bus\Query\Response;

final class AggregatesResponse implements Response
{
    private array $aggregates;

    public function __construct(AggregateResponse ...$aggregates)
    {
        $this->aggregates = $aggregates;
    }

    public function aggregates(): array
    {
        return $this->aggregates;
    }
}
