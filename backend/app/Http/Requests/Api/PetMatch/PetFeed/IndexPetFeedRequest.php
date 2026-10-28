<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\PetMatch\PetFeed;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetFeedRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'filters.breed_id' => ['sometimes', 'integer', 'exists:breeds,id'],
            'filters.size_id' => ['sometimes', 'integer', 'exists:sizes,id'],
            'filters.goal_id' => ['sometimes', 'integer', 'exists:goals,id'],
            'filters.gender' => ['sometimes', 'in:male,female'],
            'filters.max_distance_km' => ['sometimes', 'numeric', 'min:1', 'max:500'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.species_id.exists' => 'A espécie selecionada não existe.',
            'filters.breed_id.exists' => 'A raça selecionada não existe.',
            'filters.size_id.exists' => 'O porte selecionado não existe.',
            'filters.goal_id.exists' => 'O objetivo selecionado não existe.',
            'filters.gender.in' => 'O sexo deve ser macho ou fêmea.',
            'filters.max_distance_km.min' => 'A distância mínima é 1 km.',
            'filters.max_distance_km.max' => 'A distância máxima é 500 km.',
        ];
    }
}
