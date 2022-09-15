<?php

use Src\Tasks\Domain\TaskRepository;
use Src\Tasks\Infrastructure\Persistence\EloquentTaskRepository;

return [
    'binds'      => [
        //REPOSITORIES
        TaskRepository::class => EloquentTaskRepository::class,
    ],
    'singletons' => [],
];
