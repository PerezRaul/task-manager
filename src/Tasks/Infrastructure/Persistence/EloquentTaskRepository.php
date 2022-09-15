<?php

declare(strict_types=1);

namespace Src\Tasks\Infrastructure\Persistence;

use Src\Shared\Domain\Criteria\Criteria;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentCriteriaConverter;
use Src\Shared\Infrastructure\Persistence\Eloquent\EloquentRepository;
use Src\Tasks\Domain\Task;
use Src\Tasks\Domain\TaskRepository;
use Src\Tasks\Domain\Tasks;
use Src\Tasks\Infrastructure\Persistence\Eloquent\Task as EloquentTask;

use function Lambdish\Phunctional\map;

final class EloquentTaskRepository extends EloquentRepository implements TaskRepository
{
    public function save(Task $task): void
    {
        if (!$task->hasChanges()) {
            return;
        }

        EloquentTask::updateOrCreate([
            'id' => $task->id()->value(),
        ], $task->toPrimitives());
    }

    public function search(TaskId $id): ?Task
    {
        $model = EloquentTask::find($id->value());

        if (null === $model) {
            return null;
        }

        return $this->transformModelToDomainEntity($model);
    }

    public function matching(Criteria $criteria): Tasks
    {
        $query = EloquentTask::query();

        EloquentCriteriaConverter::apply($query, $criteria);

        return new Tasks(map(function (EloquentTask $model) {
            return $this->transformModelToDomainEntity($model);
        }, $query->get()->all()));
    }

    public function matchingCount(Criteria $criteria): int
    {
        $query = EloquentTask::query();

        EloquentCriteriaConverter::apply($query, $criteria);

        return $query->count('id');
    }

    public function delete(TaskId $id): void
    {
        EloquentTask::query()->where('id', $id->value())->delete();
    }

    private function transformModelToDomainEntity(EloquentTask $model): Task
    {
        return Task::fromPrimitives((array) $model->getOriginal());
    }
}
