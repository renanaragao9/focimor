<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\PetMatch\PetMessage;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetMessageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->paginationRules();
    }

    public function messages(): array
    {
        return [

        ];
    }
}
