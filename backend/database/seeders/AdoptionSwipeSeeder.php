<?php

namespace Database\Seeders;

use App\Models\AdoptionSwipe;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionSwipeSeeder extends Seeder
{
    public function run(): void
    {
        $user = fn (string $name) => User::where('email', "{$name}@focimor.com")->value('id');
        $pet = fn (string $name) => Pet::where('name', $name)->whereNotNull('ong_id')->value('id');

        $swipes = [
            ['user_id' => $user('joao'), 'pet_id' => $pet('Bidu'), 'action' => 'like'],
            ['user_id' => $user('joao'), 'pet_id' => $pet('Tom'), 'action' => 'dislike'],
            ['user_id' => $user('maria'), 'pet_id' => $pet('Nina'), 'action' => 'superlike'],
            ['user_id' => $user('maria'), 'pet_id' => $pet('Bidu'), 'action' => 'like'],
        ];

        foreach ($swipes as $data) {
            AdoptionSwipe::firstOrCreate(['user_id' => $data['user_id'], 'pet_id' => $data['pet_id']], $data);
        }
    }
}
