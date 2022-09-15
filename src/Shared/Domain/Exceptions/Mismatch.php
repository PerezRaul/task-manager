<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;
use Stringable;

final class Mismatch extends DomainException
{
    public function __construct(
        private string $valueObjectName,
        private Stringable $current,
        private Stringable $new,
    ) {
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 422;
    }

    public function errorCode(): string
    {
        return sprintf('%s_mismatch', StrUtils::snake($this->valueObjectName));
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'Attempting to change <%s> <%s> to <%s>.',
            StrUtils::headline($this->valueObjectName),
            $this->current->__toString(),
            $this->new->__toString(),
        );
    }
}
