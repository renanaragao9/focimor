<?php

namespace App\Traits;

use App\Models\Ong;
use Illuminate\Validation\ValidationException;

trait AuthorizesOngActions
{
    protected function ensureOngAdmin(Ong $ong): void
    {
        if ($ong->roleOf($this->getUserId()) !== 'admin') {
            abort(403, 'Apenas administradores da ONG podem fazer isso.');
        }
    }

    protected function ensureOngVerified(Ong $ong): void
    {
        if ($ong->status !== 'verified') {
            throw ValidationException::withMessages(['ong' => ['A ONG precisa estar verificada para publicar.']]);
        }
    }
}
