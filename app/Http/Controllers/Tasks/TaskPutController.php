<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;

use App\Http\Requests\Tasks\TaskPutRequest;
use Illuminate\Http\JsonResponse;
use Src\Shared\Domain\ArrUtils;
use Src\Shared\Domain\StrUtils;
use Src\Tasks\Application\Put\PutTaskCommand;


final class TaskPutController extends Controller
{
    public function __invoke(TaskPutRequest $request, string $taskId): JsonResponse
    {
        $validated = ArrUtils::mapWithKeys(function ($value, $key) {
            return [StrUtils::camel($key) => $value];
        }, $request->validated());

        $this->dispatch(new PutTaskCommand($taskId, ...$validated));

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
