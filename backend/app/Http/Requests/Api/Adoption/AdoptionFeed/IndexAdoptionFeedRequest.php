<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\AdoptionFeed;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexAdoptionFeedRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.species_id' => ['sometimes', 'integer', 'exists:species,id'],
            'filters.breed_id' => ['sometimes', 'integer', 'exists:breeds,id'],
            'filters.size_id' => ['sometimes', 'integer', 'exists:sizes,id'],
            'filters.gender' => ['sometimes', 'in:male,female'],
            'filters.latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:filters.longitude'],
            'filters.longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:filters.latitude'],
            'filters.max_distance_km' => ['sometimes', 'numeric', 'min:1', 'max:500'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.species_id.exists' => 'A espécie selecionada não existe.',
            'filters.breed_id.exists' => 'A raça selecionada não existe.',
            'filters.size_id.exists' => 'O porte selecionado não existe.',
            'filters.gender.in' => 'O sexo deve ser macho ou fêmea.',
            'filters.latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'filters.latitude.required_with' => 'Informe a latitude e a longitude juntas.',
            'filters.longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'filters.longitude.required_with' => 'Informe a latitude e a longitude juntas.',
            'filters.max_distance_km.min' => 'A distância mínima é 1 km.',
            'filters.max_distance_km.max' => 'A distância máxima é 500 km.',
        ];
    }
}
