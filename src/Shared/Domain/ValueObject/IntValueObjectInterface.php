<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use Stringable;

interface IntValueObjectInterface extends Stringable
{
    public function __construct(int $value);

    public function value(): int;

    public function invertSign(): static;
}
