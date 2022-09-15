<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use DateTime;
use DateTimeImmutable;
use Src\Shared\Domain\DateUtils;
use Stringable;

abstract class DatetimeValueObject implements Stringable
{
    private DateTime|DateTimeImmutable $value;

    final public function __construct(string $value, string $timezone = 'UTC')
    {
        $this->value = DateUtils::stringToDate($value, timezone: $timezone);
    }

    public static function now(): static
    {
        return new static(DateUtils::nowString());
    }

    public function timezone(string $timezone): self
    {
        return new static($this->__toString(), $timezone);
    }

    public function value(): DateTime|DateTimeImmutable
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return DateUtils::dateToString($this->value());
    }
}
