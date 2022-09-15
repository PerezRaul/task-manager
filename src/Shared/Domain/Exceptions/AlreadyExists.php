<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;
use Src\Shared\Domain\ValueObject\Uuid;

final class AlreadyExists extends DomainException
{
    private string $className;

    /**
     * @param class-string<AggregateRoot> $class
     */
    public function __construct(private string $class, private Uuid $id)
    {
        $explodedClass = explode('\\', $this->class);

        $this->className = end($explodedClass);
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return sprintf('%s_already_exists', StrUtils::snake($this->className));
    }

    protected function errorMessage(): string
    {
        return sprintf('The %s <%s> already exists.', StrUtils::headline($this->className), $this->id->value());
    }
}
