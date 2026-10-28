<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class FortalezaPetSeeder extends Seeder
{
    private const PETS_PER_BREED = 10;

    private const OWNERS = 30;

    private const LATITUDE = -3.7319;

    private const LONGITUDE = -38.5267;

    private const MALE_NAMES = ['Thor', 'Rex', 'Bob', 'Max', 'Toby', 'Bento', 'Simba', 'Zeus', 'Billy', 'Rocky', 'Nick', 'Fred', 'Apolo', 'Duque', 'Tom', 'Oliver', 'Marley', 'Pipoca', 'Jack', 'Leo'];

    private const FEMALE_NAMES = ['Luna', 'Mel', 'Mimi', 'Bella', 'Nina', 'Lola', 'Amora', 'Princesa', 'Maya', 'Bolinha', 'Cléo', 'Hera', 'Kiara', 'Pandora', 'Estrela', 'Sofia', 'Jade', 'Fiona', 'Panqueca', 'Safira'];

    private const COLORS = ['Caramelo', 'Preto', 'Branco', 'Marrom', 'Cinza', 'Preto e branco', 'Dourado', 'Rajado', 'Tricolor', 'Creme'];

    private const BIOS = [
        'Adora passear na Beira-Mar.',
        'Brincalhão e cheio de energia.',
        'Dócil e carinhoso com todo mundo.',
        'Procurando amigos para brincar na Praia de Iracema.',
        'Gosta de uma soneca depois do almoço.',
        'Curioso e sempre atento a tudo.',
        'Muito sociável com outros pets.',
        'Ama um petisco e um bom carinho.',
    ];

    public function run(): void
    {
        $now = now();
        $ownerIds = $this->owners($now);
        $sizes = DB::table('sizes')->pluck('id', 'slug');
        $goals = DB::table('goals')->whereIn('slug', ['friendship', 'breeding'])->pluck('id')->all();
        $speciesSlugs = DB::table('species')->whereIn('slug', ['dog', 'cat'])->pluck('slug', 'id');
        $existing = DB::table('pets')->whereIn('user_id', $ownerIds)->select('breed_id', DB::raw('count(*) as total'))->groupBy('breed_id')->pluck('total', 'breed_id');

        $rows = [];
        $breeds = DB::table('breeds')->whereIn('species_id', $speciesSlugs->keys())->orderBy('id')->get(['id', 'species_id']);

        foreach ($breeds as $breed) {
            $isCat = $speciesSlugs[$breed->species_id] === 'cat';
            $missing = self::PETS_PER_BREED - (int) ($existing[$breed->id] ?? 0);

            for ($i = 0; $i < $missing; $i++) {
                $gender = fake()->randomElement(['male', 'female']);
                $sizeSlug = $isCat ? fake()->randomElement(['small', 'medium']) : fake()->randomElement(['small', 'medium', 'large']);

                $rows[] = [
                    'uuid' => (string) Str::uuid(),
                    'name' => fake()->randomElement($gender === 'male' ? self::MALE_NAMES : self::FEMALE_NAMES),
                    'gender' => $gender,
                    'color' => fake()->randomElement(self::COLORS),
                    'bio' => fake()->randomElement(self::BIOS),
                    'birth_date' => fake()->dateTimeBetween('-12 years', '-6 months')->format('Y-m-d'),
                    'latitude' => round(self::LATITUDE + fake()->randomFloat(4, -0.06, 0.06), 7),
                    'longitude' => round(self::LONGITUDE + fake()->randomFloat(4, -0.06, 0.06), 7),
                    'is_neutered' => fake()->boolean(60),
                    'is_vaccinated' => fake()->boolean(85),
                    'is_active' => true,
                    'user_id' => fake()->randomElement($ownerIds),
                    'species_id' => $breed->species_id,
                    'breed_id' => $breed->id,
                    'size_id' => $sizes[$sizeSlug],
                    'goal_id' => fake()->randomElement($goals),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('pets')->insert($chunk);
        }

        $this->command?->info(count($rows).' pets criados em Fortaleza.');

        $this->call(PetPhotoSeeder::class);
    }

    private function owners($now): array
    {
        $password = Hash::make('password');
        $ids = [];

        for ($n = 1; $n <= self::OWNERS; $n++) {
            $email = "tutor{$n}.fortaleza@focimor.com";
            $id = DB::table('users')->where('email', $email)->value('id');

            $ids[] = $id ?? DB::table('users')->insertGetId([
                'name' => fake('pt_BR')->name(),
                'email' => $email,
                'password' => $password,
                'latitude' => self::LATITUDE,
                'longitude' => self::LONGITUDE,
                'email_verified_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        return $ids;
    }
}
