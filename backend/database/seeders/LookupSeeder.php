<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LookupSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // species
        $species = [
            ['name' => 'Cachorro', 'slug' => 'dog', 'order' => 1],
            ['name' => 'Gato', 'slug' => 'cat', 'order' => 2],
            ['name' => 'Outro', 'slug' => 'other', 'order' => 3],
        ];
        foreach ($species as $row) {
            DB::table('species')->updateOrInsert(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'order' => $row['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        // sizes
        $sizes = [
            ['name' => 'Pequeno', 'slug' => 'small', 'order' => 1],
            ['name' => 'Médio', 'slug' => 'medium', 'order' => 2],
            ['name' => 'Grande', 'slug' => 'large', 'order' => 3],
        ];
        foreach ($sizes as $row) {
            DB::table('sizes')->updateOrInsert(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'order' => $row['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        // goals
        $goals = [
            ['name' => 'Cruzamento', 'slug' => 'breeding', 'order' => 1],
            ['name' => 'Amizade', 'slug' => 'friendship', 'order' => 2],
            ['name' => 'Adoção', 'slug' => 'adoption', 'order' => 3],
        ];
        foreach ($goals as $row) {
            DB::table('goals')->updateOrInsert(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'order' => $row['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        $types = [
            ['name' => 'Consulta', 'slug' => 'consultation'],
            ['name' => 'Exame', 'slug' => 'exam'],
            ['name' => 'Cirurgia', 'slug' => 'surgery'],
            ['name' => 'Medicação', 'slug' => 'medication'],
            ['name' => 'Vermífugo', 'slug' => 'deworming'],
            ['name' => 'Antipulgas/carrapatos', 'slug' => 'flea_tick'],
            ['name' => 'Outro', 'slug' => 'other'],
        ];
        foreach ($types as $i => $row) {
            DB::table('health_record_types')->updateOrInsert(
                ['slug' => $row['slug']],
                ['name' => $row['name'], 'order' => $i + 1, 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }

        $dogId = DB::table('species')->where('slug', 'dog')->value('id');
        $catId = DB::table('species')->where('slug', 'cat')->value('id');
        $catalog = require __DIR__.'/data/breeds.php';

        $breeds = [];
        foreach ([$dogId => $catalog['dog'], $catId => $catalog['cat']] as $speciesId => $names) {
            $breeds[] = ['species_id' => $speciesId, 'name' => 'Vira-lata (SRD)', 'slug' => 'srd', 'order' => 1];
            foreach ($names as $i => $name) {
                $breeds[] = ['species_id' => $speciesId, 'name' => $name, 'slug' => Str::slug($name), 'order' => $i + 2];
            }
        }
        foreach ($breeds as $row) {
            DB::table('breeds')->updateOrInsert(
                ['species_id' => $row['species_id'], 'slug' => $row['slug']],
                ['name' => $row['name'], 'order' => $row['order'], 'is_active' => true, 'updated_at' => $now, 'created_at' => $now]
            );
        }
    }
}
