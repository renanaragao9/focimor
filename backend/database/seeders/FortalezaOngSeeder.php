<?php

namespace Database\Seeders;

use App\Models\Breed;
use App\Models\Goal;
use App\Models\Ong;
use App\Models\OngPost;
use App\Models\Pet;
use App\Models\Size;
use App\Models\Species;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class FortalezaOngSeeder extends Seeder
{
    private const PETS = [
        ['Thor', 'male', 'dog', 'Vira-lata (SRD)', 'large', 'Rescatado de um terreno baldio, cheio de energia.'],
        ['Mel', 'female', 'dog', 'Vira-lata (SRD)', 'medium', 'Doce e tranquila, adora colo.'],
        ['Pipoca', 'female', 'dog', 'Poodle', 'small', 'Pequena, esperta e muito sociável.'],
        ['Rex', 'male', 'dog', 'Labrador Retriever', 'large', 'Adora água e brincadeira com bola.'],
        ['Luna', 'female', 'dog', 'Shih Tzu', 'small', 'Cuidada pela equipe desde filhote.'],
        ['Fred', 'male', 'dog', 'Beagle', 'medium', 'Farejador nato, ótimo com crianças.'],
        ['Belinha', 'female', 'dog', 'Pug', 'small', 'Resgatada de maus-tratos, hoje muito afetuosa.'],
        ['Duque', 'male', 'dog', 'Golden Retriever', 'large', 'Dócil e leal, pronto para um lar com quintal.'],
        ['Mia', 'female', 'cat', 'Vira-lata (SRD)', 'small', 'Gatinha tranquila, se dá bem com outros gatos.'],
        ['Simba', 'male', 'cat', 'Vira-lata (SRD)', 'small', 'Brincalhão e curioso.'],
        ['Cleo', 'female', 'cat', 'Siamês', 'small', 'Vocal e carinhosa.'],
        ['Garfield', 'male', 'cat', 'Maine Coon', 'medium', 'Grandão, calmo e dorminhoco.'],
    ];

    public function run(): void
    {
        $ong = Ong::firstOrCreate(['slug' => 'focinho-feliz'], [
            'name' => 'Focinho Feliz',
            'cnpj' => '98.765.432/0001-10',
            'description' => 'Abrigo em Fortaleza que resgata, trata e encaminha cães e gatos para adoção responsável.',
            'email' => 'contato@focinhofeliz.org',
            'phone' => '(85) 99999-0002',
            'address' => 'Av. Beira Mar, 1000 - Meireles',
            'city' => 'Fortaleza',
            'state' => 'CE',
            'latitude' => -3.7250000,
            'longitude' => -38.4900000,
            'pix_key' => 'contato@focinhofeliz.org',
            'donation_info' => 'Precisamos de ração, areia sanitária e voluntários nos fins de semana.',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $ana = User::where('email', 'ana@focimor.com')->firstOrFail();
        $joao = User::where('email', 'joao@focimor.com')->firstOrFail();
        $ong->users()->syncWithoutDetaching([$ana->id => ['role' => 'admin'], $joao->id => ['role' => 'member']]);

        $goalId = Goal::where('slug', 'adoption')->value('id');
        $species = Species::pluck('id', 'slug');

        foreach (self::PETS as $index => [$name, $gender, $kind, $breed, $size, $bio]) {
            $breedId = Breed::where('species_id', $species[$kind])->where('slug', $breed === 'Vira-lata (SRD)' ? 'srd' : Str::slug($breed))->value('id');

            Pet::firstOrCreate(['name' => $name, 'ong_id' => $ong->id], [
                'gender' => $gender,
                'bio' => $bio,
                'birth_date' => now()->subMonths(8 + $index * 5)->toDateString(),
                'is_neutered' => $index % 3 !== 0,
                'is_vaccinated' => true,
                'is_active' => true,
                'latitude' => $ong->latitude,
                'longitude' => $ong->longitude,
                'user_id' => $ana->id,
                'species_id' => $species[$kind],
                'breed_id' => $breedId,
                'size_id' => Size::where('slug', $size)->value('id'),
                'goal_id' => $goalId,
            ]);
        }

        $posts = [
            ['Feira de adoção neste sábado', 'Vamos estar na orla com 12 pets em busca de um lar. Traga a família e venha conhecer!', 'Thor', now()->subDays(2)],
            ['Campanha de ração', 'O estoque está baixo. Qualquer quantidade ajuda os 40 animais do abrigo.', null, now()->subDays(6)],
            ['Garfield ganhou novo corte de pelo', 'Depois de uma tosa caprichada, o Garfield está pronto para posar para as fotos de adoção.', 'Garfield', now()->subDays(9)],
            ['Mutirão de vacinação (rascunho)', 'Em breve: datas, local e como se voluntariar.', null, null],
        ];

        foreach ($posts as [$title, $body, $petName, $publishedAt]) {
            OngPost::firstOrCreate(['ong_id' => $ong->id, 'title' => $title], [
                'body' => $body,
                'author_user_id' => $ana->id,
                'pet_id' => $petName ? Pet::where('ong_id', $ong->id)->where('name', $petName)->value('id') : null,
                'published_at' => $publishedAt,
            ]);
        }

        $this->call(PetPhotoSeeder::class);
    }
}
