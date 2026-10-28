<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\Ong;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.state' => ['sometimes', 'string', 'size:2'],
            'filters.city' => ['sometimes', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.state.size' => 'A UF deve ter 2 letras.',
        ];
    }
}
