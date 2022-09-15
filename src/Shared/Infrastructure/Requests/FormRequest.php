<?php

declare(strict_types=1);

namespace Src\Shared\Infrastructure\Requests;

use DateTime;
use Illuminate\Foundation\Http\FormRequest as IlluminateFormRequest;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator;
use ReflectionClass;
use RuntimeException;
use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\DateUtils;
use Src\Shared\Domain\StrUtils;

use function Lambdish\Phunctional\each;
use function Lambdish\Phunctional\map;
use function Lambdish\Phunctional\search;

/**
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
abstract class FormRequest extends IlluminateFormRequest
{
    protected array $traitsPrepare = [];
    protected array $traitsPassed  = [];

    abstract public function rules(): array;

    public function prepareForValidation(): void
    {
        $reflectionClass = new ReflectionClass($this);
        $traits          = array_keys($reflectionClass->getTraits());

        foreach ($this->traitsPrepare as $trait => $method) {
            if (in_array($trait, $traits)) {
                $this->{$method}();
            }
        }
    }

    protected function passedValidation(): void
    {
        $this->parseDates();
        $this->parseNumbers();

        $reflectionClass = new ReflectionClass($this);
        $traits          = array_keys($reflectionClass->getTraits());

        foreach ($this->traitsPassed as $trait => $method) {
            if (in_array($trait, $traits)) {
                $this->{$method}();
            }
        }
    }

    public function withValidator(Validator $validator): void
    {
        $errorsToFlatten = static::errorsToFlatten();

        if (empty($errorsToFlatten)) {
            return;
        }

        $validator->after(function (Validator $validator) use ($errorsToFlatten) {
            $errors = $validator->errors()->getMessages();

            foreach ($errors as $errorKey => $errorMessages) {
                if (!StrUtils::contains($errorKey, '.')) {
                    continue;
                }

                /** @var string|null $newErrorKey */
                $newErrorKey = search(function (string $errorToFlatten) use ($errorKey) {
                    return StrUtils::endsWith($errorKey, '.' . $errorToFlatten);
                }, $errorsToFlatten);

                if (null !== $newErrorKey) {
                    each(function (string $errorMessage) use ($validator, $errorKey, $newErrorKey) {
                        $errorMessage = str_replace(
                            $validator->getDisplayableAttribute($errorKey),
                            $validator->getDisplayableAttribute($newErrorKey),
                            $errorMessage,
                        );

                        $validator->errors()->add($newErrorKey, $errorMessage);
                    }, $errorMessages);
                }
            }
        });
    }

    public function validated($key = null, $default = null): array
    {
        return (array) parent::validated($key, $default);
    }

    public function inputArray(string $key = null, mixed $default = []): array
    {
        $input = $this->input($key, $default);

        if (!is_array($input)) {
            $input = [];
        }

        return $input;
    }

    public function inputArrayOrNull(string $key = null): ?array
    {
        return null !== $this->input($key) ? $this->inputArray($key) : null;
    }

    public function inputString(string $key = null, mixed $default = null): string
    {
        $input = $this->input($key, $default);

        if (!is_string($input)) {
            throw new RuntimeException(sprintf(
                'Input <%s> must be a string',
                $key,
            ));
        }

        return $input;
    }

    public function inputStringOrNull(string $key = null): ?string
    {
        return null !== $this->input($key) ? $this->inputString($key) : null;
    }

    public function inputInt(string $key = null, mixed $default = null): int
    {
        $input = $this->input($key, $default);

        if (!is_int($input)) {
            throw new RuntimeException(sprintf(
                'Input <%s> must be an integer',
                $key,
            ));
        }

        return $input;
    }

    public function inputIntOrNull(string $key = null): ?int
    {
        return null !== $this->input($key) ? $this->inputInt($key) : null;
    }

    public function inputFloat(string $key = null, mixed $default = null): float
    {
        $input = $this->input($key, $default);

        if (!is_numeric($input)) {
            throw new RuntimeException(sprintf(
                'Input <%s> must be a float',
                $key,
            ));
        }

        return floatval($input);
    }

    public function inputFloatOrNull(string $key = null): ?float
    {
        return null !== $this->input($key) ? $this->inputFloat($key) : null;
    }

    public function inputBool(string $key = null, mixed $default = null): bool
    {
        return filter_var($this->input($key, $default), FILTER_VALIDATE_BOOL);
    }

    public function inputBoolOrNull(string $key = null): ?bool
    {
        return null !== $this->input($key) ? $this->inputBool($key) : null;
    }

    /**
     * @param string $offset
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $parts = explode('.', $offset);

        if (count($parts) === 1) {
            parent::offsetSet($offset, $value);

            return;
        }

        $firstPart = array_shift($parts);
        $input     = $this->input($firstPart);

        ArrUtils::set($input, implode('.', $parts), $value);

        parent::offsetSet($firstPart, $input);
    }

    public function inputMap(string $key, callable $callback): void
    {
        $inputDot = ArrUtils::dot($this->inputArray());

        foreach ($inputDot as $inputKey => $inputValue) {
            $inputKeysCheck = [$inputKey];
            $keyParts       = explode('.', $inputKey);
            foreach ($keyParts as $keyPart) {
                $inputKeysCheck = array_unique(array_merge($inputKeysCheck,
                    ArrUtils::map(function ($inputKeyCheck) use ($keyPart) {
                        $value = str_replace($keyPart . '.', '*.', $inputKeyCheck);
                        $value = str_replace('.' . $keyPart, '.*', $value);

                        return $value;
                    }, $inputKeysCheck)));
            }

            if (in_array($key, $inputKeysCheck)) {
                $result = $callback($inputValue, $inputKey);
                $this->offsetSet($inputKey, $result);
                if (null !== $this->validator) {
                    /** @phpstan-ignore-next-line */
                    $this->validator->setData(array_merge($this->validator->getData(), [
                        $inputKey => $result,
                    ]));
                }
            }
        }
    }

    public static function errorsToFlatten(): array
    {
        return [];
    }

    protected function inputMatchRules(string ...$rulesCheck): array
    {
        if (empty($rulesCheck)) {
            return [];
        }

        return array_values(array_filter(map(function (string|array $rules, string $key) use ($rulesCheck) {
            $response = (new ValidationRuleParser($this->all()))->explode([$key => $rules]);

            foreach ($response->rules as $rulesRow) {
                foreach ($rulesRow as $rule) {
                    if (in_array(ValidationRuleParser::parse($rule)[0], $rulesCheck)) {
                        return $key;
                    }
                }
            }


            return null;
        }, $this->rules())));
    }

    private function parseDates(): void
    {
        $inputDates = $this->inputMatchRules('Date', 'DateFormat');

        foreach ($inputDates as $inputDate) {
            $this->inputMap($inputDate, function ($value) {
                if (!is_string($value)) {
                    return $value;
                }

                if (
                    true === str_contains($value, '+') ||
                    false !== DateTime::createFromFormat('!Y-m-d', $value)
                ) {
                    return $value;
                }

                return DateUtils::dateToString($value, timezone: date_default_timezone_get());
            });
        }
    }

    private function parseNumbers(): void
    {
        $inputIntegers = $this->inputMatchRules('Integer', 'Numeric');

        foreach ($inputIntegers as $inputInteger) {
            $this->inputMap($inputInteger, function ($value) {
                if (is_numeric($value)) {
                    return $value + 0;
                }

                return $value;
            });
        }
    }
}
