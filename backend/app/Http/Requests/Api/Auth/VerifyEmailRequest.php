<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class VerifyEmailRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'code' => ['required', 'digits:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Informe um e-mail válido.',
            'code.required' => 'O código de verificação é obrigatório.',
            'code.digits' => 'O código deve conter exatamente 6 dígitos.',
        ];
    }
}
