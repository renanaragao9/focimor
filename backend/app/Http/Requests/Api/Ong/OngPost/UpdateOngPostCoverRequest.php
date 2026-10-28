<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPost;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateOngPostCoverRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'cover' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'cover.required' => 'Envie a capa do post.',
            'cover.image' => 'A capa deve ser uma imagem.',
            'cover.mimes' => 'A capa deve ser JPG, PNG ou WEBP.',
            'cover.max' => 'A capa não pode ter mais de 5 MB.',
        ];
    }
}
