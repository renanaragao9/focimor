<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PetPhotoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        DB::table('pet_photos')->where('path', 'like', 'pets/%')->delete();
        $catalog = require __DIR__.'/data/pet-photos.php';
        $species = DB::table('species')->pluck('slug', 'id');
        $breeds = DB::table('breeds')->pluck('name', 'id');
        $withPhotos = DB::table('pet_photos')->select('pet_id')->distinct();

        $rows = [];
        DB::table('pets')
            ->whereNotIn('id', $withPhotos)
            ->orderBy('id')
            ->select('id', 'species_id', 'breed_id')
            ->each(function ($pet) use (&$rows, $catalog, $species, $breeds, $now) {
                $kind = $species[$pet->species_id] === 'cat' ? 'cat' : 'dog';
                $pool = $catalog['pool'][$kind];
                $breedName = $breeds[$pet->breed_id] ?? null;
                $first = $catalog['breeds'][$kind][$breedName] ?? $pool[$pet->id % count($pool)];
                $rows[] = ['pet_id' => $pet->id, 'path' => $first, 'order' => 0, 'is_primary' => true, 'created_at' => $now, 'updated_at' => $now];
            });

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('pet_photos')->insert($chunk);
        }

        $this->command?->info(count($rows).' fotos criadas.');
    }
}
