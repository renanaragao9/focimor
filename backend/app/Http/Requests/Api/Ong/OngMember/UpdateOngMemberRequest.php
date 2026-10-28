<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngMember;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateOngMemberRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'role' => ['required', 'in:admin,member'],
        ];
    }

    public function messages(): array
    {
        return [
            'role.required' => 'Informe o papel do membro.',
            'role.in' => 'O papel deve ser admin ou member.',
        ];
    }
}
