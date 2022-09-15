<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Traits;

use ReflectionClass;
use Src\Shared\Domain\StrUtils;
use Src\Shared\Domain\ValueObject\Exceptions\InvalidValue;

use function Lambdish\Phunctional\reindex;

trait HasValues
{
    protected static array $valuesCache = [];

    public static function values(): array
    {
        $class = static::class;

        if (!isset(self::$valuesCache[$class])) {
            $reflected                 = new ReflectionClass($class);
            self::$valuesCache[$class] = reindex(self::keysFormatter(), $reflected->getConstants());
        }

        return self::$valuesCache[$class];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private static function keysFormatter(): callable
    {
        return static fn($unused, string $key): string => StrUtils::camel(strtolower($key));
    }

    protected function throwExceptionForInvalidValue(mixed $value): never
    {
        throw new InvalidValue(static::class, strval($value));
    }

    protected function ensureIsBetweenAcceptedValues(mixed $value): void
    {
        if (!in_array($value, static::values(), true)) {
            $this->throwExceptionForInvalidValue($value);
        }
    }
}
