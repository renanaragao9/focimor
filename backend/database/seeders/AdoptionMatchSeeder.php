<?php

namespace Database\Seeders;

use App\Models\AdoptionMatch;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionMatchSeeder extends Seeder
{
    public function run(): void
    {
        $user = fn (string $name) => User::where('email', "{$name}@focimor.com")->value('id');
        $pet = fn (string $name) => Pet::where('name', $name)->whereNotNull('ong_id')->value('id');
        $ana = $user('ana');

        $matches = [
            ['user_id' => $user('joao'), 'pet_id' => $pet('Bidu'), 'status' => 'pending'],
            ['user_id' => $user('maria'), 'pet_id' => $pet('Nina'), 'status' => 'accepted', 'decided_at' => now(), 'decided_by_user_id' => $ana],
            ['user_id' => $user('maria'), 'pet_id' => $pet('Bidu'), 'status' => 'declined', 'decided_at' => now(), 'decided_by_user_id' => $ana],
        ];

        foreach ($matches as $data) {
            AdoptionMatch::firstOrCreate(['user_id' => $data['user_id'], 'pet_id' => $data['pet_id']], $data);
        }
    }
}
