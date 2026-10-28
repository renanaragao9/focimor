<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BaseService
{
    protected function getUserId(): int
    {
        return (int) ($this->getUser()->id);
    }

    protected function getUser(): User
    {
        $user = Auth::user();

        if (! $user instanceof User) {
            abort(401, 'Usuário não autenticado.');
        }

        return $user;
    }
}
