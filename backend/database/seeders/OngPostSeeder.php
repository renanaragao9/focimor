<?php

namespace Database\Seeders;

use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Database\Seeder;

class OngPostSeeder extends Seeder
{
    public function run(): void
    {
        $ong = Ong::where('slug', 'patas-amigas')->first();
        $author = User::where('email', 'ana@focimor.com')->first();

        $posts = [
            [
                'title' => 'Bidu procura uma família',
                'body' => 'O Bidu foi resgatado há 3 meses e está pronto para ser adotado. Venha conhecê-lo!',
                'pet_id' => Pet::where('name', 'Bidu')->where('ong_id', $ong->id)->value('id'),
                'published_at' => now()->subDays(3),
            ],
            [
                'title' => 'Campanha de ração',
                'body' => 'Estamos com o estoque baixo. Qualquer quantidade ajuda!',
                'pet_id' => null,
                'published_at' => now()->subDay(),
            ],
            [
                'title' => 'Mutirão de castração (rascunho)',
                'body' => 'Em breve: datas e como participar.',
                'pet_id' => null,
                'published_at' => null,
            ],
        ];

        foreach ($posts as $data) {
            OngPost::firstOrCreate(
                ['ong_id' => $ong->id, 'title' => $data['title']],
                $data + ['ong_id' => $ong->id, 'author_user_id' => $author->id]
            );
        }

        OngPost::firstOrCreate(
            ['ong_id' => null, 'title' => 'Dicas para uma adoção responsável'],
            [
                'body' => 'Antes de adotar, avalie tempo, espaço e custos. Adotar é um compromisso para a vida toda.',
                'published_at' => now(),
                'author_user_id' => User::where('email', 'aragaolabs@gmail.com')->value('id'),
            ]
        );
    }
}
