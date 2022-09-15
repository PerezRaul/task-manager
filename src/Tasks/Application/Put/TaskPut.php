<?php

declare(strict_types=1);

namespace Src\Tasks\Application\Put;

use Src\Shared\Domain\Bus\Event\EventBus;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\Task;
use Src\Tasks\Domain\TaskCreatedAt;
use Src\Tasks\Domain\TaskIsFinished;
use Src\Tasks\Domain\TaskRepository;
use Src\Tasks\Domain\TaskTitle;
use Src\Tasks\Domain\TaskUpdatedAt;

final class TaskPut
{
    public function __construct(
        private TaskRepository $repository,
        private EventBus $eventBus
    ) {
    }

    public function __invoke(
        TaskId $id,
        TaskTitle $title,
        TaskIsFinished $isFinished,
    ): void {
        $task = $this->repository->search($id);

        $task = null === $task ?
            $this->create(
                $id,
                $title,
                $isFinished,
            ) :
            $this->update(
                $task,
                $title,
                $isFinished,
            );

        $this->repository->save($task);
        $this->eventBus->publish(...$task->pullDomainEvents());
    }

    private function create(
        TaskId $id,
        TaskTitle $title,
        TaskIsFinished $isFinished,
    ): Task {
        return Task::create(
            $id,
            $title,
            $isFinished,
            TaskCreatedAt::now(),
            TaskUpdatedAt::now(),
        );
    }

    private function update(
        Task $task,
        TaskTitle $title,
        TaskIsFinished $isFinished,
    ): Task {
        $task->update(
            $title,
            $isFinished,
            TaskUpdatedAt::now(),
        );

        return $task;
    }
}
