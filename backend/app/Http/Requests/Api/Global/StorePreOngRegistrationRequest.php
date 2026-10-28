<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Global;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use App\Rules\CnpjRule;
use Illuminate\Validation\Rule;

class StorePreOngRegistrationRequest extends BaseFormRequest
{
    protected function prepareForValidation(): void
    {
        $digitsOnly = fn (mixed $value): mixed => is_string($value) ? preg_replace('/\D/', '', $value) : $value;
        $blankToNull = fn (mixed $value): mixed => is_string($value) && trim($value) === '' ? null : $value;

        $this->merge([
            'phone' => $digitsOnly($this->input('phone')),
            'cnpj' => $blankToNull($digitsOnly($this->input('cnpj'))),
            'state' => is_string($this->input('state')) ? strtoupper($this->input('state')) : $this->input('state'),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pre_ong_registrations', 'email')->whereNull('deleted_at')],
            'phone' => ['required', 'string', 'digits_between:10,11', Rule::unique('pre_ong_registrations', 'phone')->whereNull('deleted_at')],
            'description' => ['nullable', 'string', 'max:1000'],
            'cnpj' => ['nullable', 'string', new CnpjRule, Rule::unique('pre_ong_registrations', 'cnpj')->whereNull('deleted_at')],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'O nome da ONG é obrigatório.',
            'name.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail deve ter um formato válido.',
            'email.max' => 'O e-mail não pode ter mais de 255 caracteres.',
            'email.unique' => 'Já existe um pré-cadastro com este e-mail.',
            'phone.required' => 'O telefone é obrigatório.',
            'phone.digits_between' => 'O telefone deve ter 10 ou 11 dígitos.',
            'phone.unique' => 'Já existe um pré-cadastro com este telefone.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'cnpj.unique' => 'Já existe um pré-cadastro com este CNPJ.',
            'city.required' => 'A cidade é obrigatória.',
            'city.max' => 'A cidade não pode ter mais de 255 caracteres.',
            'state.required' => 'A UF é obrigatória.',
            'state.size' => 'A UF deve ter 2 letras (ex.: CE).',
        ];
    }
}
