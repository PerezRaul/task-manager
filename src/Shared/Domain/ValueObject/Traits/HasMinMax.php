<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Traits;

use InvalidArgumentException;

trait HasMinMax
{
    protected float|int|null $min = null;
    protected float|int|null $max = null;

    private function ensureValueInRange(float|int|null $value): void
    {
        if (null === $value) {
            return;
        }

        if (null !== $this->min && $value < $this->min) {
            throw new InvalidArgumentException(sprintf(
                'The value <%d> must be must be at least %d.',
                $value,
                $this->min,
            ));
        }

        if (null !== $this->max && $value > $this->max) {
            throw new InvalidArgumentException(sprintf(
                'The value <%d> must not be greater than %d.',
                $value,
                $this->max,
            ));
        }
    }
}
