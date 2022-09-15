<?php

declare(strict_types=1);

namespace Src\Tasks\Domain;

use Src\Shared\Domain\Aggregate\AggregateRoot;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\Events\TaskCreated;
use Src\Tasks\Domain\Events\TaskUpdated;

final class Task extends AggregateRoot
{
    public function __construct(
        protected TaskId $id,
        protected TaskTitle $title,
        protected TaskIsFinished $isFinished,
        protected TaskCreatedAt $createdAt,
        protected TaskUpdatedAt $updatedAt,
    ) {
    }

    public static function create(
        TaskId $id,
        TaskTitle $title,
        TaskIsFinished $isFinished,
        TaskCreatedAt $createdAt,
        TaskUpdatedAt $updatedAt,
    ): self {
        $task = new self(
            $id,
            $title,
            $isFinished,
            $createdAt,
            $updatedAt,
        );

        $task->wasRecentlyCreated = true;

        $task->record(new TaskCreated(
            $id->value(),
            $title->value(),
            $isFinished->value(),
            $createdAt->__toString(),
            $updatedAt->__toString(),
        ));

        return $task;
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            new TaskId($primitives['id']),
            new TaskTitle($primitives['title']),
            new TaskIsFinished($primitives['is_finished']),
            new TaskCreatedAt($primitives['created_at']),
            new TaskUpdatedAt($primitives['updated_at']),
        );
    }

    public function update(
        TaskId|TaskTitle|TaskIsFinished|TaskUpdatedAt ...$data
    ): void {
        $this->applyChanges(...$data);

        $this->recordOnChanges(new TaskUpdated(
            $this->id->value(),
            $this->title->value(),
            $this->isFinished->value(),
            $this->createdAt->__toString(),
            $this->updatedAt->__toString(),
            $this->changes(),
        ));
    }

    public function toPrimitives(): array
    {
        return [
            'id'          => $this->id->value(),
            'title'       => $this->title->value(),
            'is_finished' => $this->isFinished->value(),
            'created_at'  => $this->createdAt->__toString(),
            'updated_at'  => $this->updatedAt->__toString(),
        ];
    }

    public function id(): TaskId
    {
        return $this->id;
    }

    public function title(): TaskTitle
    {
        return $this->title;
    }

    public function isFinished(): TaskIsFinished
    {
        return $this->isFinished;
    }

    public function createdAt(): TaskCreatedAt
    {
        return $this->createdAt;
    }

    public function updatedAt(): TaskUpdatedAt
    {
        return $this->updatedAt;
    }
}
