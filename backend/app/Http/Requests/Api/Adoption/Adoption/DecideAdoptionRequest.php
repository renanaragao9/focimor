<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\Adoption;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class DecideAdoptionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'status' => ['required', 'in:accepted,declined,adopted'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Informe a decisão.',
            'status.in' => 'A decisão deve ser accepted, declined ou adopted.',
        ];
    }
}
