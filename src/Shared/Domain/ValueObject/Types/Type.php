<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Types;

interface Type
{
    public function passes(mixed $value): bool;
}
