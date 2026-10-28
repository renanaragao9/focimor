<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\PetMatch\PetMessage;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StorePetMessageRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'A mensagem não pode ficar vazia.',
            'body.string' => 'A mensagem deve ser um texto.',
            'body.max' => 'A mensagem não pode ter mais de 2000 caracteres.',
        ];
    }
}
