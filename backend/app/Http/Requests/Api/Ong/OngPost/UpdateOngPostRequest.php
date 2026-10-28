<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Ong\OngPost;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class UpdateOngPostRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'required', 'string', 'max:255'],
            'body' => ['sometimes', 'required', 'string', 'max:20000'],
            'published_at' => ['nullable', 'date'],
            'pet_uuid' => ['nullable', 'uuid', Rule::exists('pets', 'uuid')->where('ong_id', $this->route('managedOng')->id)->whereNull('deleted_at')],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título é obrigatório.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'body.required' => 'O conteúdo é obrigatório.',
            'body.max' => 'O conteúdo não pode ter mais de 20000 caracteres.',
            'published_at.date' => 'A data de publicação é inválida.',
            'pet_uuid.exists' => 'O pet informado não pertence a esta ONG.',
            'cover.image' => 'A capa deve ser uma imagem.',
            'cover.mimes' => 'A capa deve ser JPG, PNG ou WEBP.',
            'cover.max' => 'A capa não pode ter mais de 5 MB.',
        ];
    }
}
