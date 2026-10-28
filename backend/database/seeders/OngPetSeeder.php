<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Goal;
use App\Models\Ong;
use App\Models\Pet;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Seeder;

class OngPetSeeder extends Seeder
{
    public function run(): void
    {
        $ong = Ong::where('slug', 'patas-amigas')->first();
        $registrant = User::where('email', 'ana@focimor.com')->first();
        $dog = Species::where('slug', 'dog')->first();
        $cat = Species::where('slug', 'cat')->first();
        $srd = fn (Species $species) => Breed::where('species_id', $species->id)->where('slug', 'srd')->value('id');

        $pets = [
            ['name' => 'Bidu', 'gender' => 'male', 'bio' => 'Resgatado da rua, muito carinhoso.', 'species' => $dog, 'size' => 'medium'],
            ['name' => 'Nina', 'gender' => 'female', 'bio' => 'Calma e ótima com crianças.', 'species' => $dog, 'size' => 'small'],
            ['name' => 'Tom', 'gender' => 'male', 'bio' => 'Gato brincalhão, já castrado.', 'species' => $cat, 'size' => 'small'],
        ];

        foreach ($pets as $data) {
            Pet::firstOrCreate(
                ['name' => $data['name'], 'ong_id' => $ong->id],
                [
                    'gender' => $data['gender'],
                    'bio' => $data['bio'],
                    'is_neutered' => true,
                    'is_vaccinated' => true,
                    'is_active' => true,
                    'latitude' => $ong->latitude,
                    'longitude' => $ong->longitude,
                    'user_id' => $registrant->id,
                    'species_id' => $data['species']->id,
                    'breed_id' => $srd($data['species']),
                    'size_id' => Size::where('slug', $data['size'])->value('id'),
                    'goal_id' => Goal::where('slug', 'adoption')->value('id'),
                ]
            );
        }
    }
}
