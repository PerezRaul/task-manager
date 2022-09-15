<?php

declare(strict_types=1);

namespace Src\Tasks\Domain\Services;

use Src\Tasks\Domain\TaskRepository;
use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\Criteria\Filters;
use Src\Shared\Domain\Criteria\Groups;
use Src\Shared\Domain\Criteria\Orders;
use Src\Tasks\Domain\Tasks;

final class TasksByCriteriaSearcher
{
    public function __construct(private TaskRepository $repository)
    {
    }

    public function __invoke(Filters $filters, Orders $orders, ?int $limit, ?int $offset): Tasks
    {
        $criteria = new Criteria($filters, $orders, new Groups([]), $offset, $limit);

        return $this->repository->matching($criteria);
    }
}
