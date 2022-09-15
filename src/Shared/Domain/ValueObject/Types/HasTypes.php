<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Types;

use InvalidArgumentException;

use function Lambdish\Phunctional\each;

trait HasTypes
{
    abstract protected function allowedTypes(): ?array;

    protected function ensureValueIsOfValidType(mixed $value): void
    {
        if (null === static::allowedTypes()) {
            return;
        }

        each(function ($type) use ($value, &$isValid) {
            /** @var Type $type */
            $type = new $type();

            if ($type->passes($value)) {
                $isValid = true;
            }
        }, static::allowedTypes());

        if (!$isValid) {
            throw new InvalidArgumentException(
                sprintf('Value <%s> is not allowed.', is_array($value) ? json_encode($value) : strval($value))
            );
        }
    }
}
