<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetVaccine;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetVaccineRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return [

        ];
    }
}
