<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\MyOng;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class UpdateMyOngLogoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'Envie a logo da ONG.',
            'logo.image' => 'O arquivo deve ser uma imagem.',
            'logo.mimes' => 'A logo deve ser JPG, PNG ou WEBP.',
            'logo.max' => 'A logo não pode ter mais de 2 MB.',
        ];
    }
}
