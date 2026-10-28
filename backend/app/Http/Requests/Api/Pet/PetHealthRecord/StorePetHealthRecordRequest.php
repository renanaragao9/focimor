<?php

declare(strict_types=1);

namespace App\Http\Requests\Api\Pet\PetHealthRecord;

use App\Http\Requests\Api\Global\Base\BaseFormRequest;
use Illuminate\Validation\Rule;

class StorePetHealthRecordRequest extends BaseFormRequest
{
    public function rules(): array
    {
        return [
            'health_record_type_id' => ['required', 'integer', Rule::exists('health_record_types', 'id')->where('is_active', true)],
            'title' => ['required', 'string', 'max:255'],
            'performed_at' => ['required', 'date', 'before_or_equal:today'],
            'next_due_at' => ['nullable', 'date', 'after:performed_at'],
            'veterinarian' => ['nullable', 'string', 'max:255'],
            'cost' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'health_record_type_id.required' => 'O tipo do registro é obrigatório.',
            'health_record_type_id.exists' => 'O tipo de registro selecionado não existe.',
            'title.required' => 'O título é obrigatório.',
            'performed_at.required' => 'A data de realização é obrigatória.',
            'performed_at.date' => 'A data de realização é inválida.',
            'performed_at.before_or_equal' => 'A data de realização não pode ser futura.',
            'next_due_at.date' => 'A data do próximo retorno é inválida.',
            'next_due_at.after' => 'O próximo retorno deve ser posterior à realização.',
            'cost.numeric' => 'O custo deve ser um número.',
            'cost.min' => 'O custo não pode ser negativo.',
        ];
    }
}
