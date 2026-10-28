<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PreOngRegistration extends BaseModel
{
    public const STATUS_LABELS = [
        'pending' => 'Recebido',
        'contacted' => 'Em contato',
        'approved' => 'Aprovado',
        'rejected' => 'Não aprovado',
    ];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'description',
        'cnpj',
        'city',
        'state',
        'status',
        'notes',
        'ong_id',
    ];

    public function ong(): BelongsTo
    {
        return $this->belongsTo(Ong::class);
    }
}
