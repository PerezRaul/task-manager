<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Types;

final class StringType implements Type
{
    public function passes(mixed $value): bool
    {
        return is_string($value);
    }
}
