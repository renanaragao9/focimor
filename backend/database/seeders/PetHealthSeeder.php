<?php

namespace Database\Seeders;

use App\Models\HealthRecordType;
use App\Models\Pet;
use Illuminate\Database\Seeder;

class PetHealthSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Pet::all() as $pet) {
            $pet->detail()->firstOrCreate([], ['temperament' => 'Dócil', 'vet_name' => 'Dra. Ana']);

            $pet->vaccines()->firstOrCreate(
                ['name' => 'V10'],
                ['applied_at' => now()->subMonths(6), 'next_dose_at' => now()->addMonths(6)]
            );

            $pet->healthRecords()->firstOrCreate(
                ['title' => 'Consulta de rotina'],
                ['health_record_type_id' => HealthRecordType::where('slug', 'consultation')->value('id'), 'performed_at' => now()->subMonths(2)]
            );

            $pet->weights()->firstOrCreate(['measured_at' => now()->subMonth()->toDateString()], ['weight_kg' => 10.5]);
        }
    }
}
