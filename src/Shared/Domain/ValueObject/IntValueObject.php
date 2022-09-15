<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use Src\Shared\Domain\ValueObject\Traits\HasMinMax;

abstract class IntValueObject implements IntValueObjectInterface
{
    use HasMinMax;

    public function __construct(protected int $value)
    {
        $this->ensureValueInRange($value);
    }

    public function value(): int
    {
        return $this->value;
    }

    public function invertSign(): static
    {
        return new static(-1 * $this->value());
    }

    public function add(int|float ...$additions): static
    {
        if (empty($additions)) {
            return $this;
        }

        $value = $this->value;
        foreach ($additions as $addition) {
            $value += $addition;
        }

        return new static((int) $value);
    }

    public function subtract(int|float ...$subtractions): static
    {
        if (empty($subtractions)) {
            return $this;
        }

        $value = $this->value;
        foreach ($subtractions as $subtraction) {
            $value -= $subtraction;
        }

        return new static((int) $value);
    }

    public function multiply(int|float ...$multipliers): static
    {
        if (empty($multipliers)) {
            return $this;
        }

        $value = $this->value;
        foreach ($multipliers as $multiplier) {
            $value *= $multiplier;
        }

        return new static((int) $value);
    }

    public function divide(int|float ...$divisions): static
    {
        if (empty($divisions)) {
            return $this;
        }

        $value = $this->value;
        foreach ($divisions as $division) {
            $value /= $division;
        }

        return new static((int) $value);
    }

    public function __toString(): string
    {
        return (string) $this->value();
    }
}
