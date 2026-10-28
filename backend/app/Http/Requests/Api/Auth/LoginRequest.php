<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Auth;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use App\Models\User;
use App\Services\Api\Auth\ResendVerificationCodeService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;

class LoginRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Credenciais inválidas.',
            'email.email' => 'Credenciais inválidas.',
            'password.required' => 'Credenciais inválidas.',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $user = User::where('email', $this->input('email'))->first();

            if (! $user || ! Hash::check((string) $this->input('password'), $user->password)) {
                $validator->errors()->add('password', 'Credenciais inválidas.');

                return;
            }

            if ($user->email_verified_at === null) {
                app(ResendVerificationCodeService::class)->run(['email' => $user->email]);
                $validator->errors()->add('email', 'É necessário verificar o e-mail antes de fazer login. Um código de verificação foi enviado para seu e-mail.');
            }
        });
    }
}
