<?php

namespace Database\Seeders;

use App\Models\Pet;
use App\Models\PetMatch;
use Illuminate\Database\Seeder;

class PetMatchSeeder extends Seeder
{
    public function run(): void
    {
        $rex = Pet::where('name', 'Rex')->first();
        $luna = Pet::where('name', 'Luna')->first();

        [$petOneId, $petTwoId] = $rex->id < $luna->id
            ? [$rex->id, $luna->id]
            : [$luna->id, $rex->id];

        PetMatch::firstOrCreate(
            ['pet_one_id' => $petOneId, 'pet_two_id' => $petTwoId],
            [
                'is_active' => true,
                'matched_at' => now(),
            ]
        );
    }
}
