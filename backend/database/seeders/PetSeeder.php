<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Goal;
use App\Models\Pet;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Seeder;

class PetSeeder extends Seeder
{
    public function run(): void
    {
        $dog = Species::where('slug', 'dog')->first();
        $cat = Species::where('slug', 'cat')->first();

        $breed = fn (Species $species, string $slug) => Breed::where('species_id', $species->id)->where('slug', $slug)->value('id');
        $size = fn (string $slug) => Size::where('slug', $slug)->value('id');
        $goal = fn (string $slug) => Goal::where('slug', $slug)->value('id');

        $maria = User::where('email', 'maria@focimor.com')->first();
        $joao = User::where('email', 'joao@focimor.com')->first();
        $ana = User::where('email', 'ana@focimor.com')->first();

        $pets = [
            [
                'name' => 'Rex', 'gender' => 'male', 'bio' => 'Adoro correr no parque.',
                'latitude' => -23.5610, 'longitude' => -46.6560,
                'is_neutered' => true, 'is_vaccinated' => true, 'birth_date' => '2021-03-10',
                'user_id' => $maria->id, 'species_id' => $dog->id,
                'breed_id' => $breed($dog, 'labrador-retriever'), 'size_id' => $size('large'), 'goal_id' => $goal('friendship'),
            ],
            [
                'name' => 'Luna', 'gender' => 'female', 'bio' => 'Dócil e brincalhona.',
                'latitude' => -23.5630, 'longitude' => -46.6540,
                'is_neutered' => true, 'is_vaccinated' => true, 'birth_date' => '2022-07-22',
                'user_id' => $joao->id, 'species_id' => $dog->id,
                'breed_id' => $breed($dog, 'poodle'), 'size_id' => $size('small'), 'goal_id' => $goal('friendship'),
            ],
            [
                'name' => 'Thor', 'gender' => 'male', 'bio' => 'Procurando uma parceira para cruzar.',
                'latitude' => -23.5500, 'longitude' => -46.6600,
                'is_neutered' => false, 'is_vaccinated' => true, 'birth_date' => '2020-01-15',
                'user_id' => $ana->id, 'species_id' => $dog->id,
                'breed_id' => $breed($dog, 'bulldog'), 'size_id' => $size('medium'), 'goal_id' => $goal('breeding'),
            ],
            [
                'name' => 'Mimi', 'gender' => 'female', 'bio' => 'Gata tranquila que adora um colo.',
                'latitude' => -23.5580, 'longitude' => -46.6520,
                'is_neutered' => true, 'is_vaccinated' => true, 'birth_date' => '2023-02-05',
                'user_id' => $maria->id, 'species_id' => $cat->id,
                'breed_id' => $breed($cat, 'siames'), 'size_id' => $size('small'), 'goal_id' => $goal('friendship'),
            ],
        ];

        foreach ($pets as $data) {
            Pet::firstOrCreate(
                ['name' => $data['name'], 'user_id' => $data['user_id']],
                $data
            );
        }
    }
}
