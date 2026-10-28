<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

## Sobre o projeto

**Focimor** (de "focinho" + "amor") é um app de match para pets, com dois fluxos:

1. **Match entre pets** (cruzamento/amizade): o tutor cadastra o pet, os pets dão swipe uns nos outros (`Swipe`), o like recíproco vira `PetMatch` e os tutores conversam (`Message`).
2. **Adoção** (em construção): adotantes dão swipe em pets de doação (`AdoptionSwipe`), o dono ou a ONG decide (`AdoptionMatch`: pendente, aceito, recusado, adotado, cancelado) e a conversa reaproveita `Message` via `adoption_match_id`. Pets de adoção têm `goal = Adoção` e podem pertencer a uma `Ong`.

Monorepo: `backend/` (Laravel, este diretório) e `frontend/` (Next.js, ainda no scaffold inicial, sem funcionalidades).

### Stack do backend

PHP ^8.3, Laravel 13, Filament 5 (painel admin), Laravel Sanctum (tokens da API), spatie/laravel-activitylog (auditoria), Pint, PHPUnit. Banco SQLite por padrão, fila `database`, e-mail em `log` no `.env.example`. Idioma da aplicação: pt_BR.

### Domínio (models em `app/Models`; os de domínio estendem `BaseModel`: soft delete + activity log + bloqueio de lazy loading fora de produção)

- **Catálogos:** `Species`, `Breed`, `Size`, `Goal`, `HealthRecordType`.
- **Pets:** `Pet` (uuid, dono `user_id`, `ong_id` opcional), `PetPhoto`, e o histórico de saúde: `PetDetail` (1:1: microchip, alergias, veterinário etc.), `PetVaccine`, `PetHealthRecord` (tipo vem de `HealthRecordType`), `PetWeight` (peso, altura e largura).
- **Match entre pets:** `Swipe`, `PetMatch`, `Message`.
- **Adoção:** `AdoptionSwipe`, `AdoptionMatch`.
- **ONGs:** `Ong` (status pendente/verificada/suspensa, PIX e informações de doação) e `OngPost` (`ong_id` vazio = post do sistema; `published_at` vazio = rascunho).
- **Auth:** `User` (Sanctum `HasApiTokens`) e `EmailVerificationCode` (não usa `BaseModel` de propósito).

### Painel admin (Filament, `/admin`)

Um resource por entidade, nos grupos Cadastros, Catálogos, Interações, ONGs e Adoção, mais relation managers no Pet (fotos, vacinas, histórico de saúde, pesos) e nas mensagens dos matches. O painel é de uso exclusivo do usuário master: `User implements FilamentUser` e `canAccessPanel` retorna `is_master` (coluna booleana, fora do `$fillable`; só o `UserSeeder` define, para `aragaolabs@gmail.com`). Nunca exponha `is_master` na API nem aceite no cadastro.

### API (`routes/api.php`, prefixo `/api/v1`)

Existem a autenticação, os selects de catálogo, o CRUD de pets do dono e o match entre pets (`Api/PetMatch`). Pets (autenticados, `Api/Pet`; o pet tem `color`, texto livre até 50 caracteres): `GET|POST pets`, `GET|PUT|DELETE pets/{uuid}` e, por pet, `photos` (upload multipart, máx. 10, uma principal), `detail` (GET e PUT upsert 1:1), `vaccines`, `health-records` e `weights` (index/store/update/destroy). O parâmetro `{pet}` é resolvido em `AppServiceProvider` só para pets do usuário logado (404 para pets de terceiros) e os filhos usam `scopeBindings()`. O `ValidationException` da API usa o mesmo envelope do `BaseFormRequest` (`status`, `message`, `messages`, `errors`). A ordenação do `index` só aceita colunas da lista permitida (`SortableTrait`). Selects (públicos, só `index`, só itens ativos, `Api/Global/Select`): `GET selects/species`, `selects/breeds` (filtros opcionais `species_id` e `search`, que busca no nome; devolve no máximo 20 itens, com os que começam pelo texto primeiro), `selects/sizes`, `selects/goals` e `selects/health-record-types`. Autenticação: `auth/register`, `auth/login`, `auth/verify-email`, `auth/resend-verification-code`, `auth/forgot-password`, `auth/reset-password`, `auth/logout`, `auth/logout-all-devices` e `GET me`. O cadastro não devolve token: o e-mail precisa ser verificado (código de 6 dígitos) antes do login. Match entre pets (autenticado): `GET pets/{uuid}/feed` (pets que o pet ainda não viu, de outros usuários, ativos e fora da adoção; por padrão mesma espécie e mesmo objetivo; filtros `species_id`, `breed_id`, `size_id`, `goal_id`, `gender` e `max_distance_km`, que exige localização no pet e usa caixa no SQL + Haversine em PHP, `Support/Geo`), `POST pets/{uuid}/swipes` (`target_pet_uuid` + `action`; like/superlike recíproco cria o `PetMatch` e responde `matched: true`; sem swipe repetido, em pet próprio nem em pets de adoção), `GET matches` (filtro `filters.pet_uuid`), `GET matches/{uuid}` (um match com o outro pet e `unread_count`, para a tela de chat) e `DELETE matches/{uuid}` (desfaz: `is_active = false`). O `{petMatch}` só resolve para matches ativos do usuário. Outros usuários veem pets via `PublicPetResource`: nunca expõe `id`, dono nem latitude/longitude. Chat do match (autenticado): `GET matches/{uuid}/messages` (paginado, mais novas primeiro), `POST matches/{uuid}/messages` (`body` até 2000 caracteres, limite de 30 por minuto) e `PUT matches/{uuid}/messages/read` (marca como lidas só as do outro usuário). Só funciona em match ativo do usuário; `GET matches` traz `unread_count` e `last_message`. Adoção (autenticado, `Api/Adoption`): quem adota é qualquer usuário e quem decide é o dono do pet (`pets.user_id`; membros de ONG ficam para o item de ONGs). `GET adoptions/feed` (pets `goal = Adoção` ativos de outros usuários que o usuário ainda não viu; filtros `species_id`, `breed_id`, `size_id`, `gender` e distância via `latitude`/`longitude`/`max_distance_km` enviados pelo cliente, já que `users` não tem localização), `POST adoptions/swipes` (`pet_uuid` + `action`; like/superlike cria o `AdoptionMatch` `pending`, dislike só registra o swipe), `GET adoptions` (meus interesses, filtro `status`), `GET adoptions/received` (interesses nos meus pets, filtros `status` e `pet_uuid`; mostra só o nome do adotante), `GET adoptions/{uuid}`, `PUT adoptions/{uuid}/decision` (`accepted`, `declined` ou `adopted`, só o dono; `adopted` desativa o pet e recusa os demais interessados) e `DELETE adoptions/{uuid}` (o adotante cancela). As transições de status ficam em `AdoptionMatch::TRANSITIONS`/`canTransitionTo` (pending vai para accepted, declined ou cancelled; accepted vai para adopted, declined ou cancelled; os demais são finais). Chat: `GET|POST adoptions/{uuid}/messages` e `PUT .../messages/read`, só depois de aceito (leitura também após adotado). O `{adoptionMatch}` resolve para o adotante ou o dono do pet; ações restritas a um dos lados respondem 403. O trait `PaginatesByDistance` serve aos dois feeds. ONGs (`Api/Ong`). Não existe cadastro público de ONG: o master cria no admin e anexa membros (aba "Membros" do resource, pivô `ong_user` com papel `admin` ou `member`) e define o `status` (só `verified` aparece e publica). Público (sem login): `GET ongs` (só verificadas; busca, `filters[state]`, `filters[city]`, `adoptable_pets_count`; sem CNPJ nem coordenadas), `GET ongs/{uuid}`, `GET ongs/{uuid}/pets` (pets de adoção ativos da ONG) e `GET ongs/{uuid}/posts`; `GET posts` e `GET posts/{uuid}` (feed de posts publicados: `published_at` no passado, de ONG verificada ou do sistema, filtros `ong_uuid` e `system`). Gestão pelos membros: `GET my-ongs`, `GET|PUT my-ongs/{uuid}` (só admin altera contato, descrição, PIX e info de doação; nome, slug, CNPJ e status são do master), `POST .../logo`, `GET|POST .../members` e `PUT|DELETE .../members/{userId}` (só admin gerencia; qualquer membro pode sair; a ONG nunca fica sem admin), `GET|POST .../campaigns` e `PUT|DELETE .../campaigns/{uuid}` (caixinhas de arrecadação, qualquer membro, ONG `verified`; ver abaixo), `GET|POST .../pets` (força `goal = Adoção`, `ong_id` e ativo) e `GET|POST .../posts`, `PUT|DELETE .../posts/{uuid}`, `POST .../posts/{uuid}/cover` (rascunho = `published_at` vazio; `pet_uuid` precisa ser da própria ONG). Criar pet, criar e editar post exigem ONG `verified`. O escopo `Pet::managedBy($userId)` (pets próprios ou de ONGs em que o usuário é membro) vale para o binding `{pet}` (membros editam fotos e saúde dos pets da ONG pelas rotas `pets/...`), para os interesses de adoção recebidos, a decisão, o chat e a regra de não adotar pet próprio. Os `Route::bind` ficam em `RouteBindingServiceProvider` (`pet`, `petMatch`, `adoptionMatch`, `ong`, `managedOng`, `publicPost`). Perfil (autenticado, `Api/Profile`): `GET me` (agora com `avatar_url`, `latitude` e `longitude`), `PUT me` (só `name` e localização; `latitude` e `longitude` vêm juntas e `null` nas duas limpa; e-mail, senha e `is_master` não mudam por aqui), `POST me/avatar` (multipart, JPG/PNG/WEBP até 2 MB, apaga o arquivo anterior), `DELETE me/avatar` e `PUT me/password` (`current_password`, `password` confirmada e diferente da atual; revoga os outros tokens e mantém o atual; limite de 6 por minuto). A localização do usuário nunca aparece para outros usuários. Ela é o ponto de partida padrão da distância em `adoptions/feed` (as coordenadas enviadas na requisição têm prioridade; `max_distance_km` sem nenhuma localização dá 422) e o padrão da posição de um pet criado sem coordenadas. Trocar o e-mail e excluir a conta ainda não existem. Notificações (autenticado, `Api/Notification`): caixa de entrada no banco (canal `database`, tabela `notifications`), sem push externo por enquanto. `GET notifications` (mais novas primeiro, `filters[unread]`), `GET notifications/unread-count`, `PUT notifications/{id}/read` e `PUT notifications/read-all`; só as do próprio usuário (404 para as de outros). Cada item traz `type`, `title`, `body`, `is_read` e `data` com os uuids de referência (nada de ids internos). Gatilhos: novo match entre pets (avisa o outro dono, `new_match`), nova mensagem no match ou na adoção (`new_message`, `kind` `pet_match` ou `adoption`; só a primeira mensagem não lida de uma rajada gera aviso e quem manda não é avisado; adotante escreve avisa o dono e os membros da ONG, e o lado do pet escreve avisa o adotante), novo interesse de adoção (`adoption_interest`, avisa o dono e os membros da ONG via `Pet::managers()`) e decisão do dono (`adoption_decision`, avisa o adotante: aceito, recusado ou adotado). As classes ficam em `app/Notifications/{Pet,Adoption}` e são disparadas depois da transação dos services. Rotas públicas (sem autenticação, limite de 60 por minuto): tudo que serve a vitrine e a doação. `GET ongs`, `ongs/{uuid}`, `ongs/{uuid}/pets`, `ongs/{uuid}/posts`, `ongs/{uuid}/campaigns` (caixinhas: não mostra as canceladas), `GET posts`, `posts/{uuid}` e `GET selects/*` (catálogos, só leitura) e `GET adoptable-pets` (pets de adoção ativos de ONGs verificadas, com `search` e filtros `species_id`, `breed_id`, `size_id` e `gender`; pets de tutores não aparecem) e `adoptable-pets/{uuid}` (escopo `Pet::adoptable()`, binding `adoptablePet`, `PublicPetResource`: sem id, dono nem coordenadas). Exigem login: o feed personalizado `adoptions/feed`, demonstrar interesse (`adoptions/swipes`), os interesses, o chat e todo o resto, inclusive `my-ongs`. Faltam push (FCM/APNs), preferências de notificação e o frontend.

**Pré-cadastro de ONG (`PreOngRegistration`, tabela `pre_ong_registrations`):** inspirado no pré-cadastro de empresa do astleta. Público e sem login (`POST ong-registrations`, limite `ong-register` de 10 por minuto por IP): `name`, `email`, `phone` (só dígitos, 10 ou 11), `city`, `state` (UF), `description` e `cnpj` opcionais (dígitos verificadores validados por `App\Rules\CnpjRule`); e-mail, telefone e CNPJ únicos entre os não excluídos. A resposta devolve só `status`, `status_label` e datas, nunca dados pessoais. `GET ong-registrations/status?contact=` (e-mail ou telefone com DDD) devolve a situação (`pending` Recebido, `contacted` Em contato, `approved` Aprovado, `rejected` Não aprovado). No admin, resource "Pré-cadastros" (grupo ONGs, com selo de pendentes): o master muda a situação, anota e usa a ação "Aprovar e criar ONG" (`ApprovePreOngRegistrationService`), que cria a ONG já `verified` com slug único e, se existir usuário com o e-mail do pré-cadastro, o torna administrador da ONG. Não há notificação por e-mail ou WhatsApp ainda.

**Caixinhas das ONGs (`OngCampaign`, tabela `ong_campaigns`):** campanha de arrecadação com `title`, `description`, `goal_amount` (meta), `raised_amount` (arrecadado, atualizado à mão pela ONG ou pelo master) e `ends_at` opcional. O sistema não movimenta dinheiro: só registra o progresso. `status`: `active` (em andamento), `completed` (meta batida) e `cancelled`. Na API, sem `status` explícito, o service calcula `completed` quando `raised_amount >= goal_amount` e `active` caso contrário (uma caixinha cancelada continua cancelada até `status` ser enviado). O resource devolve `remaining_amount` e `progress_percent` (0 a 100). Admin Filament: resource "Caixinhas" no grupo ONGs. Seed: `OngCampaignSeeder`.

### Dados e testes

- `php artisan db:seed` (`DatabaseSeeder`, seguro para produção) cria só os catálogos (`LookupSeeder`) e o administrador master (`UserSeeder`, senha em `ADMIN_PASSWORD`; sem ela uma senha é gerada e mostrada no terminal). Dados fictícios (usuários de teste `maria@`, `joao@`, `ana@focimor.com` com senha `password`, pets, histórico de saúde, ONGs, posts, caixinhas e matches) ficam no `DemoSeeder`: `php artisan migrate:fresh --seed --seeder=DemoSeeder`. Nunca rode o `DemoSeeder` em produção.
- As migrations são todas `create_*` e foram editadas no lugar enquanto o projeto não está em produção.
- Testes: `tests/Unit/Models` (um por model) e `tests/Feature` (auth, seeds). Rode com `php artisan test`.

## Skills do projeto

Antes de criar ou alterar código nas áreas abaixo, leia o skill correspondente e siga o padrão dele à risca.

| Tarefa                                                                                                              | Skill                                                                        |
| ------------------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------- |
| CRUD no painel admin Filament (Migration, Model, Resource, Pages, Schemas, Tables, seeder, teste unitário do model) | [`skills/crud-filament-admin/SKILL.md`](skills/crud-filament-admin/SKILL.md) |
| API REST: Controller, FormRequests, Services, Resources, rotas `/api/v1` e respostas padronizadas                   | [`skills/SKILL.md`](skills/SKILL.md) (`crud-controller-astleta`)             |

Regras que valem sempre:

- Controller fino: cada método recebe o FormRequest e o Service em linhas separadas, com variáveis de mesmo nome da classe (`$loginRequest`, `$loginService`). Faz `$data = $xRequest->validated();`, chama `$xService->run($data)` e devolve `successResponse`/`errorResponse`.
- Regra de negócio fica nos Services. Escritas rodam em `DB::transaction`.
- **Nunca leia um valor de array direto no meio da lógica** (`$data['x']`, `$filters['x']`, `$result['x']`). Importe cada valor para uma variável no início do método e use só a variável: `$unread = $data['filters']['unread'] ?? null;`, `$perPage = (int) ($data['per_page'] ?? 15);`, `$match = $result['match'];`. Vale para Services, Controllers, Resources, Notifications e Traits. Para montar atributos, use `array_merge`/`Arr::except` em vez de gravar chaves em `$data`.
- **Ordem do bloco de importação nos Services de listagem (`Index*Service`):** `$page`, `$perPage`, `$sort`, `$direction`, `$search`, depois os filtros (`$data['filters'][...]`, na ordem em que o Request os declara) e, por último, as variáveis derivadas (`$maxDistance`, `$hasLocation` etc.). Quando o service não tem uma delas (por exemplo, sem `$sort`), pula-se a linha e mantém-se a ordem das demais. Exemplo: `$page = (int) ($data['page'] ?? 1);`, `$perPage = (int) ($data['per_page'] ?? 15);`, `$sort = $data['sort'] ?? null;`, `$direction = $data['direction'] ?? 'desc';`, `$search = $data['search'] ?? null;`, `$speciesId = $data['filters']['species_id'] ?? null;`. Nos Services de escrita, os valores de `$data` são importados primeiro, antes de autorização, `getUserId()` e `DB::transaction`, na ordem em que o Request os declara. `tests/Unit/Architecture/ServiceConventionsTest.php` verifica essas regras.
- Toda rota da API fica sob o prefixo `/api/v1` (`routes/api.php`).
- Toda tabela nova usa migration `create_*` (sem migrations `add_*`) e, com soft delete, `$table->index('deleted_at')`.
- Todo model novo tem teste em `tests/Unit/Models/`; todo endpoint novo tem teste em `tests/Feature/`.
- Antes de concluir: `./vendor/bin/pint --dirty` e `php artisan test`.
- Se um skill não cobrir uma regra que o usuário definiu, adicione a regra ao skill.
- **Mantenha este `AGENTS.md` atualizado.** Ao concluir qualquer mudança relevante, atualize a seção "Sobre o projeto" na mesma tarefa: novo model, tabela ou resource Filament, novo endpoint ou grupo de rotas na API, mudança de stack ou dependência, mudança no fluxo (match, adoção, auth), regra de acesso ao admin e novas convenções. Mudanças pequenas (ajuste de campo, correção de bug) não precisam.

## Seeds

O `DatabaseSeeder` só chama `LookupSeeder` e `UserSeeder`. Todos os seeders de dados fictícios são chamados pelo `DemoSeeder` (só para desenvolvimento): `FortalezaPetSeeder` (10 pets por raça em Fortaleza, 30 tutores `tutorN.fortaleza@focimor.com`, senha `password`) roda por último e é idempotente. Os testes `AdoptionSeedTest` e `PetHealthTest` usam o `DemoSeeder`; `AdoptionSeedTest` também garante que o `DatabaseSeeder` não cria ONGs, pets nem usuários além do admin.

**ONGs de teste:** `OngSeeder` (Patas Amigas, SP, verificada, e Lar dos Bichos, pendente) e `FortalezaOngSeeder` (Focinho Feliz, Fortaleza, verificada, 12 pets para adoção com foto e 4 posts, um deles rascunho). Em ambas, `ana@focimor.com` é admin e `joao@focimor.com` é membro (senha `password`); por isso esses dois não veem esses pets no feed de adoção, e `maria@focimor.com` (ou qualquer outra conta) vê. Todos os seeders são idempotentes.

## Fotos de pets nos seeds

`PetPhotoSeeder` grava links https públicos em `pet_photos.path` (coluna de 500 caracteres; `PetPhoto::url` devolve o link como está quando começa com http). Foto principal: imagem da raça (Wikipédia, `data/pet-photos.php`) ou, sem ela, uma de um conjunto geral (dog.ceo e TheCatAPI). Uma foto por pet, sem segunda foto repetida. Só atende pets sem foto e roda após o `FortalezaPetSeeder`.

## Armazenamento de imagens

Fotos de pets, avatares, logos e capas de posts usam o disco `config('filesystems.media')` (env `MEDIA_DISK`, padrão `public`). Nunca escreva `'public'` direto em services, resources ou Filament. Para S3: `MEDIA_DISK=s3` + `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET` (e `AWS_URL` se usar CDN); `AWS_DEFAULT_FOLDER` vira o `root` do disco `s3` (prefixo de todos os arquivos no bucket). Fotos de pets usam `GenerateTemporaryUrlHelper`, que devolve uma URL assinada quando o disco suporta URLs temporárias e mantém `Storage::url()` nos demais discos. Pacote `league/flysystem-aws-s3-v3` instalado. Testes usam `Storage::fake(config('filesystems.media'))`.

- **E-mails:** templates do Laravel publicados em `resources/views/vendor/mail` e `vendor/notifications` com a marca Focimor (faixa rosa-azul, 🐾 e 🦴, botão rosa arredondado, rodapé "Desenvolvido por AragaoLabs" e texto do link em português). Edite ali, não em `vendor/`.

- **Admin de usuários:** `App\Filament\Resources\Users` (grupo "Sistema") lista, cria, vê e edita usuários (nome, e-mail, senha opcional na edição, verificação, administrador do painel, localização). `is_master` não é fillable, então as páginas usam `forceFill`; o usuário logado não pode se excluir. Teste: `tests/Feature/Admin/UserAdminTest.php`.

- **URLs de mídia:** logos, avatares e capas de posts também saem por `GenerateTemporaryUrlHelper` (URL assinada no S3 privado; a URL pública do bucket dá 403). Não use `Storage::url()` direto nos resources.

- `GET my-ongs/{ong}/posts/{post}` mostra um post da ONG (inclusive rascunho e agendado) para qualquer membro.

- **Adoção direta (`direct: true` em `POST adoptions/swipes`):** é o botão "Quero adotar" da página do pet e prevalece sobre um swipe anterior: um `dislike` vira `like` e cria o interesse; interesse `pending`, `accepted` ou `adopted` responde 422 "Você já entrou em contato…"; `cancelled` volta para `pending`; `declined` continua bloqueado ("O responsável recusou…"). Sem `direct`, o swipe repetido segue dando 422.
