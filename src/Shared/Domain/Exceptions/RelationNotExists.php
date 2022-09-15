<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;
use Stringable;

final class RelationNotExists extends DomainException
{
    private string $className;

    /**
     * @param class-string<AggregateRoot> $class
     */
    public function __construct(
        private string $class,
        private Stringable $relationId,
        private Stringable $relationType,
    ) {
        $explodedClass = explode('\\', $this->class);

        $this->className = end($explodedClass);
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 404;
    }

    public function errorCode(): string
    {
        return sprintf('%s_relation_not_exists', StrUtils::snake($this->className));
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The %s relation with id %s and type %s is invalid.',
            StrUtils::headline($this->className),
            $this->relationId->__toString(),
            $this->relationType->__toString(),
        );
    }
}
