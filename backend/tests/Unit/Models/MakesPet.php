<?php

namespace Tests\Unit\Models;

use App\Models\Goal;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Illuminate\Support\Str;

trait MakesPet
{
    protected function makePet(): Pet
    {
        $user = User::create([
            'name' => 'Tutor',
            'email' => 'tutor'.Str::random(6).'@example.com',
            'password' => 'password',
        ]);

        $species = Species::create([
            'name' => 'Cachorro', 'slug' => 'dog-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        $goal = Goal::create([
            'name' => 'Amizade', 'slug' => 'friendship-'.Str::random(5), 'order' => 1, 'is_active' => true,
        ]);

        return Pet::create([
            'name' => 'Rex',
            'gender' => 'male',
            'is_active' => true,
            'user_id' => $user->id,
            'species_id' => $species->id,
            'goal_id' => $goal->id,
        ]);
    }
}
