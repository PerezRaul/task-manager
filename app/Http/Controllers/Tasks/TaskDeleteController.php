<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tasks;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Src\Tasks\Application\Delete\DeleteTaskCommand;

final class TaskDeleteController extends Controller
{
    public function __invoke(string $wikiCategoryId): JsonResponse
    {
        $this->dispatch(new DeleteTaskCommand($wikiCategoryId));

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }
}
