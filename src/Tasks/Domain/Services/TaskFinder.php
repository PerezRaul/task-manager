<?php

declare(strict_types=1);

namespace Src\Tasks\Domain\Services;

use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\Task;
use Src\Tasks\Domain\TaskRepository;
use Src\Shared\Domain\Exceptions\NotExists;

final class TaskFinder
{
    public function __construct(private TaskRepository $repository)
    {
    }

    public function __invoke(TaskId $id): Task
    {
        return $this->repository->search($id) ?? throw new NotExists(Task::class, $id);
    }
}
