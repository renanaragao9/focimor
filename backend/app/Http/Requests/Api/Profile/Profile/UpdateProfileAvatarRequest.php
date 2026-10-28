<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Profile\Profile;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateProfileAvatarRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.required' => 'Envie a foto de perfil.',
            'avatar.image' => 'O arquivo deve ser uma imagem.',
            'avatar.mimes' => 'A foto deve ser JPG, PNG ou WEBP.',
            'avatar.max' => 'A foto não pode ter mais de 2 MB.',
        ];
    }
}
