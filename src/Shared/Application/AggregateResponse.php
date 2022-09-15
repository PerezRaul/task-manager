<?php

declare(strict_types=1);

namespace Src\Shared\Application;

use Src\Shared\Domain\Aggregate\Aggregates\AggregateColumns;
use Src\Shared\Domain\Aggregate\Aggregates\AggregateCount;
use Src\Shared\Domain\Bus\Query\Response;

final class AggregateResponse implements Response
{
    public function __construct(private AggregateCount $count, private AggregateColumns $columns)
    {
    }

    public function count(): int
    {
        return $this->count->value();
    }

    public function columns(): array
    {
        return $this->columns->value();
    }
}
