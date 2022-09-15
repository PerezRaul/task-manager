<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Criteria;

use RuntimeException;

abstract class FilterParserFactory
{
    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    public static function get(array $filters, bool $strict = true): array
    {
        $mappedFilters = [];

        foreach ($filters as $key => $value) {
            if (!isset(static::mapping()[$key])) {
                if ($strict) {
                    throw new RuntimeException(sprintf('No mapping for <%s> filter.', $key));
                }

                continue;
            }

            $mappingClass = static::mapping()[$key];

            $mappedFilter = $mappingClass::get($value);

            if (null !== $mappedFilter) {
                array_push($mappedFilters, $mappedFilter);
            }
        }

        return $mappedFilters;
    }

    abstract protected static function mapping(): array;
}
