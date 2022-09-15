<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use Src\Shared\Domain\ValueObject\Traits\HasValues;
use Stringable;

abstract class Enum implements Stringable
{
    use HasValues;

    final public function __construct(protected mixed $value)
    {
        $this->ensureIsBetweenAcceptedValues($value);
    }

    public function value(): mixed
    {
        return $this->value;
    }

    public function equals(Enum $other): bool
    {
        return get_class($other) === get_class($this) && $this->value() === $other->value();
    }

    public static function fromString(mixed $value): static
    {
        return new static($value);
    }

    public static function randomValue(): mixed
    {
        return self::values()[array_rand(self::values())];
    }

    public static function random(): static
    {
        return new static(self::randomValue());
    }

    public static function __callStatic(string $name, mixed $args): static
    {
        return new static(self::values()[$name]);
    }

    public function __toString(): string
    {
        return strval($this->value());
    }
}
