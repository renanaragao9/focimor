<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\Swipe;
use Illuminate\Database\Seeder;

class SwipeSeeder extends Seeder
{
    public function run(): void
    {
        $rex = Pet::where('name', 'Rex')->first();
        $luna = Pet::where('name', 'Luna')->first();
        $thor = Pet::where('name', 'Thor')->first();
        $mimi = Pet::where('name', 'Mimi')->first();

        $swipes = [
            // Like recíproco entre Rex e Luna -> vira match.
            ['swiper_pet_id' => $rex->id, 'target_pet_id' => $luna->id, 'action' => 'like'],
            ['swiper_pet_id' => $luna->id, 'target_pet_id' => $rex->id, 'action' => 'superlike'],

            // Like unilateral: Thor curte Luna, mas Luna ainda nao respondeu.
            ['swiper_pet_id' => $thor->id, 'target_pet_id' => $luna->id, 'action' => 'like'],

            // Dislike: Rex nao curtiu Thor.
            ['swiper_pet_id' => $rex->id, 'target_pet_id' => $thor->id, 'action' => 'dislike'],

            // Mimi curte Rex (unilateral).
            ['swiper_pet_id' => $mimi->id, 'target_pet_id' => $rex->id, 'action' => 'like'],
        ];

        foreach ($swipes as $data) {
            Swipe::firstOrCreate(
                ['swiper_pet_id' => $data['swiper_pet_id'], 'target_pet_id' => $data['target_pet_id']],
                $data
            );
        }
    }
}
