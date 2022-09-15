<?php

declare(strict_types=1);

namespace App\Http\Requests;

/**
 * @mixin FormRequest
 */
trait PermissionableRequestTrait
{
    public function permissionableKeys(): array
    {
        return array_keys($this->rules());
    }
}
