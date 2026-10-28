<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngMember;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreOngMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'role' => ['required', 'in:admin,member'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Informe o e-mail do membro.',
            'email.email' => 'Informe um e-mail válido.',
            'role.required' => 'Informe o papel do membro.',
            'role.in' => 'O papel deve ser admin ou member.',
        ];
    }
}
