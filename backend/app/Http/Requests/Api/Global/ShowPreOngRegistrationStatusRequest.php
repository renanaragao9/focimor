<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Global;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class ShowPreOngRegistrationStatusRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'contact' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'contact.required' => 'Informe o e-mail ou o telefone usado no pré-cadastro.',
        ];
    }
}
