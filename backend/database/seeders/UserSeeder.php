<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $email = 'aragaolabs@gmail.com';

        $existing = User::where('email', $email)->first();

        if ($existing) {
            $existing->forceFill(['is_master' => true])->save();
            $this->command?->info("Usuário {$email} já existe. Garantido como master.");

            return;
        }

        $password = config('app.admin_password');

        if (blank($password)) {
            $password = Str::password(20, symbols: false);
            $this->command?->warn("ADMIN_PASSWORD não definida. Senha gerada para {$email}: {$password}");
        }

        User::forceCreate([
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make((string) $password),
            'email_verified_at' => now(),
            'is_master' => true,
        ]);
    }
}
