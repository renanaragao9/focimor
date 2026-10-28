<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngMember;

use App\Models\Ong;
use App\Models\User;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UpdateOngMemberService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, User $user, array $data): User
    {
        $role = $data['role'];

        $this->ensureOngAdmin($managedOng);

        $isLastAdmin = $managedOng->roleOf($user->id) === 'admin'
            && $managedOng->users()->wherePivot('role', 'admin')->count() === 1;

        if ($isLastAdmin && $role !== 'admin') {
            throw ValidationException::withMessages(['role' => ['A ONG precisa de pelo menos um administrador.']]);
        }

        return DB::transaction(function () use ($managedOng, $user, $role): User {
            $managedOng->users()->updateExistingPivot($user->id, ['role' => $role]);

            return $managedOng->users()->where('users.id', $user->id)->firstOrFail();
        });
    }
}
