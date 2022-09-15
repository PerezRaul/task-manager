<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Delete;

use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\Events\TaskDeleted;
use Src\Tasks\Domain\Services\TaskFinder;
use Src\Tasks\Domain\TaskRepository;

final class TaskDelete
{
    public function __construct(
        private TaskRepository $repository,
        private EventBus $eventBus,
        private TaskFinder $finder,
    ) {
    }

    public function __invoke(TaskId $id): void
    {
        $task = $this->finder->__invoke($id);

        $this->repository->delete($task->id());

        $this->eventBus->publish(
            new TaskDeleted(
                $task->id()->value(),
                $task->title()->value(),
                $task->isFinished()->value(),
                $task->createdAt()->__toString(),
                $task->updatedAt()->__toString(),
            )
        );
    }
}
