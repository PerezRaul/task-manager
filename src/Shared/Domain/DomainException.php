<?php

declare(strict_types=1);

namespace Src\Shared\Domain;

use DomainException as BaseDomainException;

/** @SuppressWarnings(PHPMD.NumberOfChildren) */
abstract class DomainException extends BaseDomainException
{
    public function __construct()
    {
        parent::__construct($this->errorMessage());
    }

    public function statusCode(): int
    {
        return 500;
    }

    abstract public function errorCode(): string;

    abstract protected function errorMessage(): string;

    protected function errorMessageReplacements(): array
    {
        return [];
    }
}
