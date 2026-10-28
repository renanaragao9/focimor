<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            LookupSeeder::class,
            UserSeeder::class,
            TestUserSeeder::class,
            PetSeeder::class,
            PetPhotoSeeder::class,
            PetHealthSeeder::class,
            SwipeSeeder::class,
            PetMatchSeeder::class,
            MessageSeeder::class,
            OngSeeder::class,
            OngPetSeeder::class,
            OngPostSeeder::class,
            AdoptionSwipeSeeder::class,
            AdoptionMatchSeeder::class,
            AdoptionMessageSeeder::class,
            FortalezaOngSeeder::class,
            OngCampaignSeeder::class,
            FortalezaPetSeeder::class,
        ]);
    }
}
