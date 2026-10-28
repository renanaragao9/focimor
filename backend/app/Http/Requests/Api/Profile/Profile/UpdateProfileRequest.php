<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Profile\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateProfileRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'latitude.numeric' => 'A latitude é inválida.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'latitude.required_with' => 'Informe a latitude e a longitude juntas.',
            'longitude.numeric' => 'A longitude é inválida.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
            'longitude.required_with' => 'Informe a latitude e a longitude juntas.',
        ];
    }
}
