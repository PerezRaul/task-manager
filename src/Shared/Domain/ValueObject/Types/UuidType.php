<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Types;

use Ramsey\Uuid\Uuid as RamseyUuid;

final class UuidType implements Type
{
    public function passes(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        return RamseyUuid::isValid($value);
    }
}
