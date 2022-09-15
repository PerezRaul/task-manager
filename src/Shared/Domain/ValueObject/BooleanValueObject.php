<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use Stringable;

abstract class BooleanValueObject implements Stringable
{
    public function __construct(protected bool $value)
    {
    }

    public function value(): bool
    {
        return $this->value;
    }

    public function isTrue(): bool
    {
        return $this->value() === true;
    }

    public function isFalse(): bool
    {
        return !$this->isTrue();
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}
