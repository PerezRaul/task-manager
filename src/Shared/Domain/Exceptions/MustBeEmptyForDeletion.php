<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;

final class MustBeEmptyForDeletion extends DomainException
{
    private string $className;
    private string $classCheckedName;

    /**
     * @param class-string<AggregateRoot> $class
     */
    public function __construct(private string $class, private string $classChecked)
    {
        $explodedClass        = explode('\\', $this->class);
        $explodedClassChecked = explode('\\', $this->classChecked);

        $this->className        = end($explodedClass);
        $this->classCheckedName = end($explodedClassChecked);
        parent::__construct();
    }

    public function statusCode(): int
    {
        return 409;
    }

    public function errorCode(): string
    {
        return sprintf(
            '%s_must_be_empty_of_%s',
            StrUtils::snake($this->className),
            StrUtils::snake($this->classCheckedName),
        );
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The %s could not be deleted. Must not have %s associated.',
            StrUtils::headline($this->className),
            StrUtils::headline($this->classCheckedName),
        );
    }
}
