<?php

declare(strict_types=1);

namespace Src\Shared\Domain\Exceptions;

use Src\Shared\Domain\Criteria\Filters;
use Src\Shared\Domain\DomainException;
use Src\Shared\Domain\StrUtils;

final class ByCriteriaNotExists extends DomainException
{
    private string $className;

    public function __construct(private string $class, private Filters $filters)
    {
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
        return sprintf('%s_by_criteria_not_exits', StrUtils::snake($this->className));
    }

    protected function errorMessage(): string
    {
        return sprintf(
            'The %s <%s> does not exists.',
            StrUtils::headline($this->className),
            $this->filters->serialize(),
        );
    }
}
