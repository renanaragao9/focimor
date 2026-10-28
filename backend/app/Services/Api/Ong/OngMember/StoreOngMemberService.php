<?php

declare(strict_types=1);

namespace App\Services\Api\Ong\OngMember;

use App\Models\Ong;
use App\Models\User;
use App\Services\BaseService;
use App\Traits\AuthorizesOngActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreOngMemberService extends BaseService
{
    use AuthorizesOngActions;

    public function run(Ong $managedOng, array $data): User
    {
        $email = $data['email'];
        $role = $data['role'];

        $this->ensureOngAdmin($managedOng);

        $user = User::where('email', $email)->first();

        if (! $user) {
            throw ValidationException::withMessages(['email' => ['Nenhum usuário encontrado com este e-mail.']]);
        }

        if ($managedOng->roleOf($user->id) !== null) {
            throw ValidationException::withMessages(['email' => ['Este usuário já é membro da ONG.']]);
        }

        return DB::transaction(function () use ($managedOng, $user, $role): User {
            $managedOng->users()->attach($user->id, ['role' => $role]);

            return $managedOng->users()->where('users.id', $user->id)->firstOrFail();
        });
    }
}
