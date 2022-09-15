<?php

declare(strict_types=1);

namespace Src\Tasks\Application\CountByCriteria;

use Src\Tasks\Domain\TaskRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\Criteria\Filters;
use Src\Shared\Domain\Criteria\Groups;
use Src\Shared\Domain\Criteria\Orders;

final class TasksByCriteriaCounter
{
    public function __construct(private TaskRepository $repository)
    {
    }

    public function __invoke(Filters $filters): int
    {
        $criteria = new Criteria($filters, new Orders([]), new Groups([]), null, null);

        return $this->repository->matchingCount($criteria);
    }
}
