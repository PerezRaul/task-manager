<?php

declare(strict_types=1);

namespace Src\Tasks\Domain;

use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\Tasks\TaskId;

interface TaskRepository
{
    public function save(Task $task): void;

    public function search(TaskId $id): ?Task;

    public function matching(Criteria $criteria): Tasks;

    public function matchingCount(Criteria $criteria): int;

    public function delete(TaskId $id): void;
}
