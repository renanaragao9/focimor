<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngMember;

use App\Models\Ong;
use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeleteOngMemberService extends BaseService
{
    public function run(Ong $managedOng, User $user): void
    {
        $isSelf = $user->id === $this->getUserId();

        if (! $isSelf && $managedOng->roleOf($this->getUserId()) !== 'admin') {
            abort(403, 'Apenas administradores da ONG podem fazer isso.');
        }

        $isLastAdmin = $managedOng->roleOf($user->id) === 'admin'
            && $managedOng->users()->wherePivot('role', 'admin')->count() === 1;

        if ($isLastAdmin) {
            throw ValidationException::withMessages(['user' => ['A ONG precisa de pelo menos um administrador.']]);
        }

        DB::transaction(fn () => $managedOng->users()->detach($user->id));
    }
}
