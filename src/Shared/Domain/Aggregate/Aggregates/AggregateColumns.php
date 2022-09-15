<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Aggregate\Aggregates;

use Src\Shared\Domain\ValueObject\ArrayValueObject;

final class AggregateColumns extends ArrayValueObject
{
    protected int $minLength = 0;

    protected function allowedTypes(): ?array
    {
        return null;
    }
}
