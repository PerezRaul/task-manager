<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use DateTime;
use DateTimeImmutable;
use Stringable;

final class FilterValue
{
    public function __construct(protected DateTime|DateTimeImmutable|array|string|Stringable|int|float|bool|null $value)
    {
    }

    public function value(): DateTime|DateTimeImmutable|array|string|Stringable|int|float|bool|null
    {
        return $this->value;
    }
}
