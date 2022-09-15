<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;
use JetBrains\PhpStorm\ArrayShape;

trait Pagination
{
    protected function emptyResponse(Request $request): JsonResponse
    {
        return new JsonResponse([
            'data' => [],
            'meta' => $this->paginationMeta(
                0,
                $this->perPage($request),
                $this->page($request)
            ),
        ], JsonResponse::HTTP_OK);
    }

    protected function offset(Request $request): int
    {
        return (int) (
            ($this->page($request) * $this->perPage($request)) - $this->perPage($request)
        );
    }

    protected function page(Request $request): int
    {
        return (int) $request->get('page', 1);
    }

    protected function perPage(Request $request): int
    {
        return (int) $request->get('per_page', 20);
    }

    /** @SuppressWarnings(PHPMD.BooleanArgumentFlag) */
    protected function validatePagination(Request $request, bool $validate = true): Validator
    {
        $validator = ValidatorFacade::make($request->all(), [
            'per_page' => 'nullable|integer|min:5|max:500',
            'page'     => 'nullable|integer',
        ]);

        if ($validate) {
            $validator->validate();
        }

        return $validator;
    }

    #[ArrayShape([
        'current_page' => "int",
        'per_page'     => "int",
        'last_page'    => "float",
        'from'         => "float|int",
        'to'           => "float|int",
        'total'        => "int"
    ])] protected function paginationMeta(
        int $numberItems,
        int $perPage,
        int $page
    ): array {
        $to   = $perPage * $page;
        $from = $to - $perPage + 1;

        return [
            'current_page' => $page,
            'per_page'     => $perPage,
            'last_page'    => ceil($numberItems / $perPage),
            'from'         => $from,
            'to'           => $to,
            'total'        => $numberItems,
        ];
    }
}
