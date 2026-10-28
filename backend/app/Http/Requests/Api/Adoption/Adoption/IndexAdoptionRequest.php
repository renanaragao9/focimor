<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\Adoption;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexAdoptionRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.status' => ['sometimes', 'in:pending,accepted,declined,adopted,cancelled'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.status.in' => 'O status informado é inválido.',
        ];
    }
}
