<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\PetMatch;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $match = PetMatch::with(['petOne.user', 'petTwo.user'])->first();

        if (! $match) {
            return;
        }

        $userOne = $match->petOne->user_id;
        $userTwo = $match->petTwo->user_id;

        $messages = [
            ['sender_user_id' => $userOne, 'body' => 'Oi! Nossos pets deram match 🐾', 'read_at' => now()],
            ['sender_user_id' => $userTwo, 'body' => 'Que fofo! Vamos marcar um passeio no parque?', 'read_at' => now()],
            ['sender_user_id' => $userOne, 'body' => 'Bora! Sábado de manhã funciona pra você?', 'read_at' => null],
        ];

        foreach ($messages as $data) {
            Message::firstOrCreate(
                ['pet_match_id' => $match->id, 'sender_user_id' => $data['sender_user_id'], 'body' => $data['body']],
                $data + ['pet_match_id' => $match->id]
            );
        }
    }
}
