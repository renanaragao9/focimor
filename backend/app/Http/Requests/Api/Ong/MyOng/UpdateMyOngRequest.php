<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\MyOng;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateMyOngRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string', 'max:5000'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'size:2'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'pix_key' => ['nullable', 'string', 'max:255'],
            'donation_info' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.email' => 'Informe um e-mail válido.',
            'state.size' => 'A UF deve ter 2 letras.',
            'latitude.between' => 'A latitude deve estar entre -90 e 90.',
            'longitude.between' => 'A longitude deve estar entre -180 e 180.',
        ];
    }
}
