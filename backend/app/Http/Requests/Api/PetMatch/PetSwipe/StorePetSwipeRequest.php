<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\PetMatch\PetSwipe;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class StorePetSwipeRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'target_pet_uuid' => ['required', 'uuid', Rule::exists('pets', 'uuid')->where('is_active', true)->whereNull('deleted_at')],
            'action' => ['required', 'in:like,dislike,superlike'],
        ];
    }

    public function messages(): array
    {
        return [
            'target_pet_uuid.required' => 'Informe o pet alvo.',
            'target_pet_uuid.uuid' => 'O pet alvo é inválido.',
            'target_pet_uuid.exists' => 'O pet alvo não foi encontrado.',
            'action.required' => 'Informe se você curtiu, passou ou super curtiu.',
            'action.in' => 'A ação deve ser like, dislike ou superlike.',
        ];
    }
}
