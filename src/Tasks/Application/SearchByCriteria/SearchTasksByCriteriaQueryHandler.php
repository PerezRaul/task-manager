<?php

declare(strict_types=1);

namespace Src\Tasks\Application\SearchByCriteria;

use Src\Tasks\Application\TaskResponse;
use Src\Tasks\Application\TasksResponse;
use Src\Tasks\Domain\Task;
use Src\Tasks\Domain\Services\TasksByCriteriaSearcher;
use Src\Shared\Domain\Bus\Query\QueryHandler;
use Src\Shared\Domain\Criteria\Filters;
use Src\Shared\Domain\Criteria\Orders;

use function Lambdish\Phunctional\map;

final class SearchTasksByCriteriaQueryHandler implements QueryHandler
{
    public function __construct(private TasksByCriteriaSearcher $searcher)
    {
    }

    public function __invoke(SearchTasksByCriteriaQuery $query): TasksResponse
    {
        $filters = Filters::fromValues($query->filters());
        $orders  = Orders::fromValues($query->orders());

        $tasks = $this->searcher->__invoke($filters, $orders, $query->limit(), $query->offset());

        return new TasksResponse(...map($this->toResponse(), $tasks));
    }

    private function toResponse(): callable
    {
        return fn(Task $task) => new TaskResponse(
            $task->id(),
            $task->title(),
            $task->isFinished(),
            $task->createdAt(),
            $task->updatedAt(),
        );
    }
}
