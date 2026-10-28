<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Profile\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfilePasswordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->mixedCase()->numbers()],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required' => 'Informe a senha atual.',
            'current_password.current_password' => 'A senha atual está incorreta.',
            'password.required' => 'A nova senha é obrigatória.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.different' => 'A nova senha deve ser diferente da atual.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.',
            'password.mixed' => 'A senha deve ter letras maiúsculas e minúsculas.',
            'password.numbers' => 'A senha deve ter pelo menos um número.',
        ];
    }
}
