<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPublicCampaign;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngPublicCampaignRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return $this->paginationRules();
    }

    public function messages(): array
    {
        return [];
    }
}
