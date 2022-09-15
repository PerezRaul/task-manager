<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

interface ArrayValueObjectInterface
{
    public function __construct(array $value);

    public function value(): array;

    public function json(): string;

    public function add(array $items): static;

    public function set(string $key, mixed $setValue): static;

    public function forget(string ...$keys): static;

    public function forgetValue(string ...$values): static;

    public function count(): int;

    public function isEmpty(): bool;
}
