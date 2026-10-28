<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPublicPost;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexOngPublicPostRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'search' => ['nullable', 'string', 'max:255'],
        ]);
    }

    public function messages(): array
    {
        return [

        ];
    }
}
