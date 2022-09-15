<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

interface I18nValueObjectInterface
{
    public function __construct(array $value);

    public function value(): array;

    public function json(): string;

    public function add(string $language, mixed $addValue): static;

    public function get(string $language, string $fallbackLanguage = 'es'): mixed;
}
