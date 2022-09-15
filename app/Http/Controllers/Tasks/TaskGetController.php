<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use Src\Tasks\Application\Find\FindTaskQuery;
use Src\Tasks\Application\TaskResponse;
use Illuminate\Http\JsonResponse;

final class TaskGetController extends Controller
{
    public function __invoke(string $taskId): JsonResponse
    {
        /** @var TaskResponse $task */
        $task = $this->ask(new FindTaskQuery($taskId));

        return new JsonResponse([
            'data' => [
                'id'          => $task->id(),
                'title'       => $task->title(),
                'is_finished' => $task->isFinished(),
                'created_at'  => $task->createdAt(),
                'updated_at'  => $task->updatedAt(),
            ],
        ], JsonResponse::HTTP_OK);
    }
}
