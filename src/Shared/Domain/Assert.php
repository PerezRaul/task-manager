<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use InvalidArgumentException;

final class Assert
{
    public static function arrayOf(array $items, mixed ...$classes): void
    {
        foreach ($items as $item) {
            self::instanceOneOf($item, ...$classes);
        }
    }

    public static function instanceOneOf(mixed $item, mixed ...$classes): void
    {
        $isInstanceOneOf = false;
        foreach ($classes as $class) {
            if ($item instanceof $class) {
                $isInstanceOneOf = true;
            }
        }

        if (false === $isInstanceOneOf) {
            throw new InvalidArgumentException(
                sprintf(
                    'The item <%s> is not an instance of one of <%s>',
                    is_object($item) ? get_class($item) : strval($item),
                    implode(', ', $classes),
                ),
            );
        }
    }
}
