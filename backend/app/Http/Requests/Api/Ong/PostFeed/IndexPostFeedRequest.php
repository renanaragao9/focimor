<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\PostFeed;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPostFeedRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.ong_uuid' => ['sometimes', 'uuid'],
            'filters.system' => ['sometimes', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.ong_uuid.uuid' => 'A ONG informada é inválida.',
        ];
    }
}
