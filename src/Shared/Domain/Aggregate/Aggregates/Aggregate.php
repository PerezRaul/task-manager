<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate\Aggregates;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\ArrUtils;

class Aggregate extends AggregateRoot
{
    public function __construct(
        private AggregateCount $count,
        private AggregateColumns $columns,
    ) {
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            new AggregateCount($primitives['count']),
            new AggregateColumns(ArrUtils::except($primitives, 'count')),
        );
    }

    public function count(): AggregateCount
    {
        return $this->count;
    }

    public function columns(): AggregateColumns
    {
        return $this->columns;
    }
}
