<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;
use Stringable;

final class AlreadyInUse extends DomainException
{
    public function __construct(private string $valueObjectName, private Stringable $valueObject)
    {
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return sprintf('%s_already_in_use', $this->field());
    }

    public function field(): string
    {
        return StrUtils::snake($this->valueObjectName);
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The %s <%s> must be unique.',
            StrUtils::headline($this->valueObjectName),
            $this->valueObject->__toString()
        );
    }
}
