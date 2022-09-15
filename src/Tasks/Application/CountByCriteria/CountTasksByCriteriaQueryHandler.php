<?php

declare(strict_types=1);

namespace Src\Tasks\Application\CountByCriteria;

use Src\Shared\Application\CounterResponse;
use Src\Shared\Domain\Bus\Query\QueryHandler;
use Src\Shared\Domain\Criteria\Filters;

final class CountTasksByCriteriaQueryHandler implements QueryHandler
{
    public function __construct(private TasksByCriteriaCounter $counter)
    {
    }

    public function __invoke(CountTasksByCriteriaQuery $query): CounterResponse
    {
        $filters = Filters::fromValues($query->filters());

        return new CounterResponse($this->counter->__invoke($filters));
    }
}
