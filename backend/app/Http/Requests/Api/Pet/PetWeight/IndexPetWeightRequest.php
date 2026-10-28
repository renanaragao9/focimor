<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetWeight;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetWeightRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
        ]);
    }

    public function messages(): array
    {
        return [

        ];
    }
}
