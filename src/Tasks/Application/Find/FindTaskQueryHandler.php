<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Find;

use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Application\TaskResponse;
use Src\Tasks\Domain\Services\TaskFinder;
use Src\Shared\Domain\Bus\Query\QueryHandler;

final class FindTaskQueryHandler implements QueryHandler
{
    public function __construct(private TaskFinder $finder)
    {
    }

    public function __invoke(FindTaskQuery $query): TaskResponse
    {
        $task = $this->finder->__invoke(new TaskId($query->id()));

        return new TaskResponse(
            $task->id(),
            $task->title(),
            $task->isFinished(),
            $task->createdAt(),
            $task->updatedAt(),
        );
    }
}
