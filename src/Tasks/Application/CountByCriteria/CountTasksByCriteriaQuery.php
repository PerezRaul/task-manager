<?php

declare(strict_types=1);

namespace Src\Tasks\Application\CountByCriteria;

use Src\Shared\Domain\Bus\Query\Query;

final class CountTasksByCriteriaQuery implements Query
{
    public function __construct(private array $filters)
    {
    }

    public function filters(): array
    {
        return $this->filters;
    }
}
