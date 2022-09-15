<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class FilterParser
{
    abstract public static function get(mixed $value): ?array;
}
