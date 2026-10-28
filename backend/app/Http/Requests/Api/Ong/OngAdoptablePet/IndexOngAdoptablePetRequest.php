<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngAdoptablePet;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngAdoptablePetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'filters.gender' => ['sometimes', 'in:male,female'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.species_id.exists' => 'A espécie selecionada não existe.',
            'filters.gender.in' => 'O sexo deve ser macho ou fêmea.',
        ];
    }
}
