<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetWeight;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StorePetWeightRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'weight_kg' => ['required', 'numeric', 'min:0.01', 'max:9999.99'],
            'height_cm' => ['nullable', 'numeric', 'min:0.01', 'max:9999.99'],
            'width_cm' => ['nullable', 'numeric', 'min:0.01', 'max:9999.99'],
            'measured_at' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'weight_kg.required' => 'O peso é obrigatório.',
            'weight_kg.numeric' => 'O peso deve ser um número.',
            'weight_kg.min' => 'O peso deve ser maior que zero.',
            'height_cm.min' => 'A altura deve ser maior que zero.',
            'width_cm.min' => 'A largura deve ser maior que zero.',
            'measured_at.required' => 'A data da medição é obrigatória.',
            'measured_at.date' => 'A data da medição é inválida.',
            'measured_at.before_or_equal' => 'A data da medição não pode ser futura.',
        ];
    }
}
