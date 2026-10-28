<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\PetMatch\PetMatch;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetMatchRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.pet_uuid' => ['sometimes', 'uuid'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.pet_uuid.uuid' => 'O pet informado é inválido.',
        ];
    }
}
