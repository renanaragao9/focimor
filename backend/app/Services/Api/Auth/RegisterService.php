<?php

declare(strict_types=1);

namespace App\Services\Api\Auth;

use App\Models\User;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class RegisterService extends BaseService
{
    public function __construct(private readonly SendVerificationCodeService $sendCode) {}

    public function run(array $data): User
    {
        $name = $data['name'];
        $email = $data['email'];
        $password = $data['password'];

        return DB::transaction(function () use ($name, $email, $password): User {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $this->sendCode->run($user);

            return $user;
        });
    }
}
