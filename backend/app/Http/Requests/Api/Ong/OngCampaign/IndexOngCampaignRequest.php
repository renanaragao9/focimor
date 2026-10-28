<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngCampaign;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngCampaignRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.status' => ['sometimes', 'in:active,completed,cancelled'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.status.in' => 'A situação informada é inválida.',
        ];
    }
}
