<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\Adoption;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexReceivedAdoptionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.status' => ['sometimes', 'in:pending,accepted,declined,adopted,cancelled'],
            'filters.pet_uuid' => ['sometimes', 'uuid'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.status.in' => 'O status informado é inválido.',
            'filters.pet_uuid.uuid' => 'O pet informado é inválido.',
        ];
    }
}
