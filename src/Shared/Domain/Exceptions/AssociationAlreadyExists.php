<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;
use Stringable;

final class AssociationAlreadyExists extends DomainException
{
    private string $classOneName;
    private string $classTwoName;

    /**
     * @param class-string<AggregateRoot> $classOne
     * @param class-string<AggregateRoot> $classTwo
     */
    public function __construct(
        private string $classOne,
        private string $classTwo,
        private Stringable $valueObjectOne,
        private Stringable $valueObjectTwo,
    ) {
        $explodedClassOne   = explode('\\', $this->classOne);
        $this->classOneName = end($explodedClassOne);

        $explodedClassTwo   = explode('\\', $this->classTwo);
        $this->classTwoName = end($explodedClassTwo);
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return sprintf(
            '%s_%s_association_already_exists',
            StrUtils::snake($this->classOneName),
            StrUtils::snake($this->classTwoName),
        );
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The association with %s <%s> and %s <%s> already exists.',
            StrUtils::headline($this->classOneName),
            $this->valueObjectOne->__toString(),
            StrUtils::headline($this->classTwoName),
            $this->valueObjectTwo->__toString(),
        );
    }
}
