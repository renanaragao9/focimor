<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngCampaign;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateOngCampaignRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'goal_amount' => ['sometimes', 'required', 'numeric', 'min:1', 'max:9999999999'],
            'raised_amount' => ['sometimes', 'required', 'numeric', 'min:0', 'max:9999999999'],
            'status' => ['sometimes', 'in:active,completed,cancelled'],
            'ends_at' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 5000 caracteres.',
            'goal_amount.required' => 'Informe a meta da caixinha.',
            'goal_amount.min' => 'A meta deve ser de pelo menos R$ 1,00.',
            'raised_amount.required' => 'Informe o valor arrecadado.',
            'raised_amount.min' => 'O valor arrecadado não pode ser negativo.',
            'status.in' => 'A situação deve ser active, completed ou cancelled.',
            'ends_at.date' => 'A data de encerramento é inválida.',
        ];
    }
}
