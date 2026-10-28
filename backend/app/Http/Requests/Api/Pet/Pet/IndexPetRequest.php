<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\Pet;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'filters.goal_id' => ['sometimes', 'integer', 'exists:goals,id'],
            'filters.is_active' => ['sometimes', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.species_id.exists' => 'A espécie selecionada não existe.',
            'filters.goal_id.exists' => 'O objetivo selecionado não existe.',
        ];
    }
}
