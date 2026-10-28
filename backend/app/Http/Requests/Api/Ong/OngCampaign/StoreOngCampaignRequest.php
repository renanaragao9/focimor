<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngCampaign;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StoreOngCampaignRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'goal_amount' => ['required', 'numeric', 'min:1', 'max:9999999999'],
            'raised_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 5000 caracteres.',
            'goal_amount.required' => 'Informe a meta da caixinha.',
            'goal_amount.numeric' => 'A meta deve ser um valor numérico.',
            'goal_amount.min' => 'A meta deve ser de pelo menos R$ 1,00.',
            'raised_amount.numeric' => 'O valor arrecadado deve ser numérico.',
            'raised_amount.min' => 'O valor arrecadado não pode ser negativo.',
            'ends_at.date' => 'A data de encerramento é inválida.',
            'ends_at.after_or_equal' => 'A data de encerramento não pode ser passada.',
        ];
    }
}
