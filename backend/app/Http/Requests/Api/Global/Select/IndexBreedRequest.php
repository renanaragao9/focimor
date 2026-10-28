<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Global\Select;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexBreedRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'species_id' => ['nullable', 'integer', 'exists:species,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'search.string' => 'A busca informada é inválida.',
            'search.max' => 'A busca deve ter no máximo 100 caracteres.',
            'species_id.integer' => 'A espécie informada é inválida.',
            'species_id.exists' => 'A espécie selecionada não existe.',
        ];
    }
}
