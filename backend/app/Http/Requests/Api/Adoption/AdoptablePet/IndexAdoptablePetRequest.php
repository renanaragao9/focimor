<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\AdoptablePet;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexAdoptablePetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'filters.breed_id' => ['sometimes', 'integer', 'exists:breeds,id'],
            'filters.size_id' => ['sometimes', 'integer', 'exists:sizes,id'],
            'filters.gender' => ['sometimes', 'in:male,female'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.species_id.exists' => 'A espécie selecionada não existe.',
            'filters.breed_id.exists' => 'A raça selecionada não existe.',
            'filters.size_id.exists' => 'O porte selecionado não existe.',
            'filters.gender.in' => 'O sexo deve ser macho ou fêmea.',
        ];
    }
}
