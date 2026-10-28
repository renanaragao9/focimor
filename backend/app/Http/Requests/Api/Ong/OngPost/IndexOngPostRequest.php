<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPost;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngPostRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.published' => ['sometimes', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [

        ];
    }
}
