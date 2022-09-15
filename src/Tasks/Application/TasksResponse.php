<?php

declare(strict_types=1);

namespace Src\Tasks\Application;

use Src\Shared\Domain\Bus\Query\Response;

final class TasksResponse implements Response
{
    private array $tasks;

    public function __construct(TaskResponse ...$tasks)
    {
        $this->tasks = $tasks;
    }

    public function tasks(): array
    {
        return $this->tasks;
    }
}
