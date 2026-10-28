<?php

namespace Database\Seeders;

use App\Models\AdoptionMatch;
use App\Models\Message;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdoptionMessageSeeder extends Seeder
{
    public function run(): void
    {
        $match = AdoptionMatch::where('status', 'accepted')->first();

        if (! $match) {
            return;
        }

        $ana = User::where('email', 'ana@focimor.com')->value('id');

        $messages = [
            ['sender_user_id' => $ana, 'body' => 'Oi Maria! Aceitamos seu interesse na Nina. Quando pode visitar?', 'read_at' => now()],
            ['sender_user_id' => $match->user_id, 'body' => 'Que ótimo! Posso ir sábado às 10h.', 'read_at' => null],
        ];

        foreach ($messages as $data) {
            Message::firstOrCreate(
                ['adoption_match_id' => $match->id, 'sender_user_id' => $data['sender_user_id'], 'body' => $data['body']],
                $data + ['adoption_match_id' => $match->id]
            );
        }
    }
}
