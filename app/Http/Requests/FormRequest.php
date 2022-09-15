<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Src\Shared\Infrastructure\Requests\FormRequest as SharedFormRequest;

abstract class FormRequest extends SharedFormRequest
{
    protected array $traitsPrepare = [
        PermissionableRequestTrait::class            => 'addDefaultsToRequest',
        PermissionableRequestWithDefaultTrait::class => 'addDefaultsToRequest',
    ];
}
