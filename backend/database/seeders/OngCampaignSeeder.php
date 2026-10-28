<?php

namespace Database\Seeders;

use App\Models\Ong;
use App\Models\OngCampaign;
use Illuminate\Database\Seeder;

class OngCampaignSeeder extends Seeder
{
    private const CAMPAIGNS = [
        'patas-amigas' => [
            ['Castração solidária', 'Vamos castrar 40 cães e gatos resgatados antes de irem para adoção. Cada R$ 150 cobre uma cirurgia.', 6000, 4350, 45],
            ['Ração para o inverno', 'O estoque de ração do abrigo acaba em três semanas. Ajude a alimentar 60 animais.', 2500, 2500, null],
        ],
        'focinho-feliz' => [
            ['Reforma do canil', 'Precisamos trocar o telhado e o piso do canil, que alagam nas chuvas.', 15000, 6200, 90],
            ['Tratamento do Thor', 'Despesas com exames e medicação de um resgatado que precisa de tratamento antes da adoção.', 1800, 900, 20],
            ['Vacinação em dia', 'Vacinas anuais para todos os animais do abrigo.', 3200, 410, null],
        ],
    ];

    public function run(): void
    {
        foreach (self::CAMPAIGNS as $slug => $campaigns) {
            $ong = Ong::where('slug', $slug)->first();

            if (! $ong) {
                continue;
            }

            foreach ($campaigns as [$title, $description, $goal, $raised, $days]) {
                OngCampaign::firstOrCreate(['ong_id' => $ong->id, 'title' => $title], [
                    'description' => $description,
                    'goal_amount' => $goal,
                    'raised_amount' => $raised,
                    'status' => $raised >= $goal ? 'completed' : 'active',
                    'ends_at' => $days ? now()->addDays($days)->toDateString() : null,
                ]);
            }
        }
    }
}
