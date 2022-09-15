<?php

declare(strict_types=1);

namespace Src\Shared\Domain\ValueObject\Exceptions;

use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;

final class InvalidValue extends DomainException
{
    private string $className;

    /**
     * @param class-string $class
     */
    public function __construct(private string $class, private mixed $value)
    {
        $explodedClass = explode('\\', $this->class);

        $this->className = end($explodedClass);
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return sprintf('invalid_%s_value', StrUtils::snake($this->className));
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The value <%s> is invalid.',
            is_scalar($this->value) ? $this->value : json_encode($this->value),
        );
    }
}
