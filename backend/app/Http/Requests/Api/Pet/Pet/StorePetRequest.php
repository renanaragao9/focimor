<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\Pet;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class StorePetRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female'],
            'color' => ['nullable', 'string', 'max:50'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'birth_date' => ['nullable', 'date', 'before_or_equal:today'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'is_neutered' => ['boolean'],
            'is_vaccinated' => ['boolean'],
            'is_active' => ['boolean'],
            'species_id' => ['required', 'integer', 'exists:species,id'],
            'breed_id' => ['nullable', 'integer', Rule::exists('breeds', 'id')->where('species_id', $this->input('species_id'))],
            'size_id' => ['nullable', 'integer', 'exists:sizes,id'],
            'goal_id' => ['required', 'integer', 'exists:goals,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome do pet é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'gender.required' => 'O sexo do pet é obrigatório.',
            'gender.in' => 'O sexo deve ser macho ou fêmea.',
            'color.max' => 'A cor não pode ter mais de 50 caracteres.',
            'bio.max' => 'A bio não pode ter mais de 2000 caracteres.',
            'birth_date.date' => 'A data de nascimento é inválida.',
            'birth_date.before_or_equal' => 'A data de nascimento não pode ser futura.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'species_id.required' => 'A espécie é obrigatória.',
            'species_id.exists' => 'A espécie selecionada não existe.',
            'breed_id.exists' => 'A raça selecionada não existe para a espécie informada.',
            'size_id.exists' => 'O porte selecionado não existe.',
            'goal_id.required' => 'O objetivo é obrigatório.',
            'goal_id.exists' => 'O objetivo selecionado não existe.',
        ];
    }
}
