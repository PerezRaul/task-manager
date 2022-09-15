<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate\Aggregates;

use Src\Shared\Domain\Collection;

class Aggregates extends Collection
{
    protected function types(): array
    {
        return [
            Aggregate::class,
        ];
    }
}
