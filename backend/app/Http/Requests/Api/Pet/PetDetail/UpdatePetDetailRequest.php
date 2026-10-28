<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetDetail;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdatePetDetailRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'microchip' => ['nullable', 'string', 'max:255', Rule::unique('pet_details', 'microchip')->ignore($this->route('pet')->detail?->id)],
            'pedigree_number' => ['nullable', 'string', 'max:255'],
            'coat' => ['nullable', 'string', 'max:255'],
            'blood_type' => ['nullable', 'string', 'max:255'],
            'temperament' => ['nullable', 'string', 'max:255'],
            'allergies' => ['nullable', 'string', 'max:2000'],
            'chronic_conditions' => ['nullable', 'string', 'max:2000'],
            'diet' => ['nullable', 'string', 'max:2000'],
            'vet_name' => ['nullable', 'string', 'max:255'],
            'vet_clinic' => ['nullable', 'string', 'max:255'],
            'vet_phone' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'microchip.unique' => 'Este microchip já está cadastrado em outro pet.',
            'microchip.max' => 'O microchip não pode ter mais de 255 caracteres.',
            'allergies.max' => 'As alergias não podem ter mais de 2000 caracteres.',
            'chronic_conditions.max' => 'As condições crônicas não podem ter mais de 2000 caracteres.',
            'diet.max' => 'A alimentação não pode ter mais de 2000 caracteres.',
            'notes.max' => 'As observações não podem ter mais de 5000 caracteres.',
        ];
    }
}
