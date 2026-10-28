<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetPhoto;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;

class StorePetPhotoRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'order' => ['nullable', 'integer', 'min:0', 'max:65535'],
            'is_primary' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'Envie a foto do pet.',
            'photo.image' => 'O arquivo deve ser uma imagem.',
            'photo.mimes' => 'A foto deve ser JPG, PNG ou WEBP.',
            'photo.max' => 'A foto não pode ter mais de 5 MB.',
            'order.integer' => 'A ordem deve ser um número inteiro.',
        ];
    }
}
