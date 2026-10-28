<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetVaccine;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdatePetVaccineRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'applied_at' => ['sometimes', 'required', 'date', 'before_or_equal:today'],
            'next_dose_at' => ['nullable', 'date', 'after:'.($this->input('applied_at') ?? $this->route('vaccine')->applied_at->toDateString())],
            'batch' => ['nullable', 'string', 'max:255'],
            'veterinarian' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da vacina é obrigatório.',
            'applied_at.required' => 'A data de aplicação é obrigatória.',
            'applied_at.date' => 'A data de aplicação é inválida.',
            'applied_at.before_or_equal' => 'A data de aplicação não pode ser futura.',
            'next_dose_at.date' => 'A data da próxima dose é inválida.',
            'next_dose_at.after' => 'A próxima dose deve ser posterior à aplicação.',
        ];
    }
}
