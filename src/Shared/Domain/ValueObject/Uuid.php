<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use InvalidArgumentException;
use Ramsey\Uuid\Uuid as RamseyUuid;

class Uuid extends StringValueObject
{
    final public function __construct(protected string $value)
    {
        if (strlen($value) !== 36) {
            $this->value = $value = RamseyUuid::fromBytes($value)->__toString();
        }

        $this->ensureIsValidUuid($value);
        parent::__construct($value);
    }

    public static function random(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function equals(Uuid|NullableUuid $other): bool
    {
        return $this->value() === $other->value();
    }

    public function getBytes(): string
    {
        return RamseyUuid::fromString($this->value)->getBytes();
    }

    private function ensureIsValidUuid(string $value): void
    {
        if (!RamseyUuid::isValid($value)) {
            throw new InvalidArgumentException(sprintf('<%s> does not allow the value <%s>.', static::class, $value));
        }
    }
}
