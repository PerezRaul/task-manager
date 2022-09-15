<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject;

use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\ValueObject\Types\HasTypes;
use InvalidArgumentException;
use RuntimeException;

use function Lambdish\Phunctional\each;

abstract class I18nValueObject implements I18nValueObjectInterface
{
    use HasTypes;

    public const AVAILABLE_LANGUAGES = ['en', 'es', 'ca'];
    private array $value;

    public function __construct(array $value)
    {
        $this->ensureIsI18n($value);
        each(function ($valueItem) {
            $this->ensureValueIsOfValidType($valueItem);
        }, $value);
        $this->value = $value;
    }

    public function value(): array
    {
        return $this->value;
    }

    public function json(): string
    {
        $json = json_encode($this->value);

        if (false === $json) {
            throw new RuntimeException('Unable to encode i18n value.');
        }

        return $json;
    }

    public function add(string $language, mixed $addValue): static
    {
        $value = $this->value();

        $value[$language] = $addValue;

        return new static($value);
    }

    public function get(string $language, string $fallbackLanguage = 'es'): mixed
    {
        if (ArrUtils::has($this->value, $language)) {
            return ArrUtils::get($this->value, $language);
        }

        if (ArrUtils::has($this->value, $fallbackLanguage)) {
            return ArrUtils::get($this->value, $fallbackLanguage);
        }

        return ArrUtils::first($this->value);
    }

    private function ensureIsI18n(array $value): void
    {
        $diff = array_diff(array_keys($value), self::AVAILABLE_LANGUAGES);
        if (!empty($diff)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid language codes, allowed values are <%s>.',
                implode(', ', self::AVAILABLE_LANGUAGES)
            ));
        }
    }
}
