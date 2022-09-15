<?php

declare(strict_types=1);

namespace App\Http\Requests\Tasks;

use App\Http\Requests\FormRequest;

final class TaskPutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'title'       => 'required|string',
            'is_finished' => 'required|boolean',
        ];
    }
}
