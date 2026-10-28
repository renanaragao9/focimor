<?php

namespace Database\Seeders;

use App\Models\Ong;
use App\Models\User;
use Illuminate\Database\Seeder;

class OngSeeder extends Seeder
{
    public function run(): void
    {
        $ongs = [
            [
                'slug' => 'patas-amigas',
                'name' => 'Patas Amigas',
                'cnpj' => '12.345.678/0001-90',
                'description' => 'Resgatamos e cuidamos de cães e gatos abandonados até encontrarem um lar.',
                'email' => 'contato@patasamigas.org',
                'phone' => '(11) 99999-0001',
                'address' => 'Rua das Flores, 100',
                'city' => 'São Paulo',
                'state' => 'SP',
                'latitude' => -23.5505200,
                'longitude' => -46.6333080,
                'pix_key' => 'contato@patasamigas.org',
                'donation_info' => 'Aceitamos ração, cobertores e medicamentos. Doações em PIX ajudam com as castrações.',
                'status' => 'verified',
                'verified_at' => now(),
            ],
            [
                'slug' => 'lar-dos-bichos',
                'name' => 'Lar dos Bichos',
                'description' => 'Abrigo novo, ainda aguardando verificação.',
                'city' => 'Campinas',
                'state' => 'SP',
                'status' => 'pending',
            ],
        ];

        foreach ($ongs as $data) {
            Ong::firstOrCreate(['slug' => $data['slug']], $data);
        }

        $ana = User::where('email', 'ana@focimor.com')->first();
        $joao = User::where('email', 'joao@focimor.com')->first();
        $patas = Ong::where('slug', 'patas-amigas')->first();

        $patas->users()->syncWithoutDetaching([
            $ana->id => ['role' => 'admin'],
            $joao->id => ['role' => 'member'],
        ]);
    }
}
