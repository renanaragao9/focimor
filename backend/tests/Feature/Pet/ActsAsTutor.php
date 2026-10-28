<?php

declare(strict_types=1);

namespace Tests\Feature\Pet;

use App\Models\Goal;
use App\Models\Pet;
use App\Models\Species;
use App\Models\User;
use Database\Seeders\LookupSeeder;

trait ActsAsTutor
{
    protected User $tutor;

    protected function loginAsTutor(string $email = 'tutor@example.com'): User
    {
        $this->seed(LookupSeeder::class);
        $this->tutor = $this->makeUser($email);
        $this->withToken($this->tutor->createToken('t')->plainTextToken);

        return $this->tutor;
    }

    protected function makeUser(string $email): User
    {
        return User::create(['name' => 'Tutor', 'email' => $email, 'password' => 'Senha123']);
    }

    protected function makePetFor(User $user, array $overrides = []): Pet
    {
        return Pet::create(array_merge([
            'name' => 'Rex',
            'gender' => 'male',
            'user_id' => $user->id,
            'species_id' => Species::where('slug', 'dog')->value('id'),
            'goal_id' => Goal::where('slug', 'friendship')->value('id'),
        ], $overrides));
    }

    protected function petPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Thor',
            'gender' => 'male',
            'color' => 'Caramelo',
            'bio' => 'Adora passear.',
            'birth_date' => '2022-05-10',
            'latitude' => -23.55,
            'longitude' => -46.63,
            'is_neutered' => true,
            'is_vaccinated' => true,
            'species_id' => Species::where('slug', 'dog')->value('id'),
            'goal_id' => Goal::where('slug', 'friendship')->value('id'),
        ], $overrides);
    }

    protected function switchTo(User $user): void
    {
        $this->app['auth']->forgetGuards();
        $this->withToken($user->createToken('t2')->plainTextToken);
    }
}
