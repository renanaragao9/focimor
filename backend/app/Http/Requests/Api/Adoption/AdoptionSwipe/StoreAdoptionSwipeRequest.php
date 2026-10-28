<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Adoption\AdoptionSwipe;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class StoreAdoptionSwipeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'pet_uuid' => ['required', 'uuid', Rule::exists('pets', 'uuid')->where('is_active', true)->whereNull('deleted_at')],
            'action' => ['required', 'in:like,dislike,superlike'],
            'direct' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'pet_uuid.required' => 'Informe o pet.',
            'pet_uuid.uuid' => 'O pet informado é inválido.',
            'pet_uuid.exists' => 'O pet não foi encontrado ou já não está disponível.',
            'action.required' => 'Informe se você curtiu, passou ou super curtiu.',
            'action.in' => 'A ação deve ser like, dislike ou superlike.',
        ];
    }
}
