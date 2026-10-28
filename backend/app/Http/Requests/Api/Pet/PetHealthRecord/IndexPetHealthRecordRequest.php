<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetHealthRecord;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexPetHealthRecordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
            'filters.health_record_type_id' => ['sometimes', 'integer', 'exists:health_record_types,id'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.health_record_type_id.exists' => 'O tipo de registro selecionado não existe.',
        ];
    }
}
