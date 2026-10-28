<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Notification\Notification;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class IndexNotificationRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return array_merge($this->paginationRules(), [
            'filters.unread' => ['sometimes', 'boolean'],
        ]);
    }

    public function messages(): array
    {
        return [
            'filters.unread.boolean' => 'O filtro de não lidas é inválido.',
        ];
    }
}
