<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Types;

use Src\Shared\Domain\DateUtils;
use Throwable;

final class DateTimeType implements Type
{
    public function passes(mixed $value): bool
    {
        if (!is_string($value)) {
            return false;
        }

        try {
            DateUtils::stringToDate($value);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
