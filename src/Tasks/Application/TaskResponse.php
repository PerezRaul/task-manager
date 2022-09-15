<?php

declare(strict_types=1);

namespace Src\Tasks\Application;


use Src\Shared\Domain\Bus\Query\Response;
use Src\Shared\Domain\Tasks\TaskId;
use Src\Tasks\Domain\TaskCreatedAt;
use Src\Tasks\Domain\TaskIsFinished;
use Src\Tasks\Domain\TaskTitle;
use Src\Tasks\Domain\TaskUpdatedAt;

final class TaskResponse implements Response
{
    public function __construct(
        private TaskId $id,
        private TaskTitle $title,
        private TaskIsFinished $isFinished,
        private TaskCreatedAt $createdAt,
        private TaskUpdatedAt $updatedAt,
    ) {
    }

    public function id(): string
    {
        return $this->id->value();
    }

    public function title(): string
    {
        return $this->title->value();
    }

    public function isFinished(): bool
    {
        return $this->isFinished->value();
    }

    public function createdAt(): string
    {
        return $this->createdAt->__toString();
    }

    public function updatedAt(): string
    {
        return $this->updatedAt->__toString();
    }
}
