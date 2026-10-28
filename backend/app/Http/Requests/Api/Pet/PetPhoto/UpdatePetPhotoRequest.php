<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetPhoto;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdatePetPhotoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'order' => ['sometimes', 'integer', 'min:0', 'max:65535'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'order.integer' => 'A ordem deve ser um número inteiro.',
        ];
    }
}
