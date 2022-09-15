<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Pagination;
use App\Http\Requests\Tasks\TasksGetRequest;
use Illuminate\Http\JsonResponse;
use Src\Shared\Application\CounterResponse;
use Src\Shared\Domain\ArrUtils;
use Src\Tasks\Application\CountByCriteria\CountTasksByCriteriaQuery;
use Src\Tasks\Application\SearchByCriteria\SearchTasksByCriteriaQuery;
use Src\Tasks\Application\TaskResponse;
use Src\Tasks\Application\TasksResponse;
use Src\Tasks\Domain\Filters\TaskFilterParserFactory;

use function Lambdish\Phunctional\map;

final class TasksGetController extends Controller
{
    use Pagination;

    public function __invoke(TasksGetRequest $request): JsonResponse
    {
        $filters = TaskFilterParserFactory::get(array_merge(
            ArrUtils::except($request->validated(), 'page', 'per_page'),
            [],
        ));

        /** @var CounterResponse $numberTasks */
        $numberTasks = $this->ask(new CountTasksByCriteriaQuery($filters));

        if ($numberTasks->total() === 0) {
            return $this->emptyResponse($request);
        }

        /** @var TasksResponse $tasks */
        $tasks = $this->ask(new SearchTasksByCriteriaQuery(
            $filters,
            [['created_at', 'desc']],
            $this->perPage($request),
            $this->offset($request)
        ));

        return new JsonResponse([
            'data' => map(
                $this->taskResponse(),
                $tasks->tasks()
            ),
            'meta' => $this->paginationMeta(
                $numberTasks->total(),
                $this->perPage($request),
                $this->page($request)
            ),
        ], JsonResponse::HTTP_OK);
    }

    private function taskResponse(): callable
    {
        return fn(TaskResponse $task) => [
            'id'          => $task->id(),
            'title'       => $task->title(),
            'is_finished' => $task->isFinished(),
            'created_at'  => $task->createdAt(),
            'updated_at'  => $task->updatedAt(),
        ];
    }
}
