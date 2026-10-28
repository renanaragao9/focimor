<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPet;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngPetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.is_active' => ['sometimes', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [

        ];
    }
}
