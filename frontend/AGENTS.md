<!-- BEGIN:nextjs-agent-rules -->

## This is NOT the Next.js you know

This version has breaking changes — APIs, conventions, and file structure may all differ from your training data. Read the relevant guide in `node_modules/next/dist/docs/` (resolved from this file's directory; in monorepos the `next` package may not be visible from the repo root) before writing any code. Heed deprecation notices.

This block is written and re-added by `next dev` — verify at `node_modules/next/dist/server/lib/generate-agent-files.js`. Removing it from a diff only re-creates the uncommitted change; committing it with your work keeps the tree clean.

<!-- END:nextjs-agent-rules -->

## Frontend do Focimor

Next.js 16 (App Router, React 19, TypeScript), Tailwind CSS 4 e shadcn/ui (estilo `radix-nova`, configurado em `components.json`). Os componentes foram trazidos do front do astleta e seguem a mesma base.

- **Componentes de UI:** ficam em `src/components/ui` (alert, avatar, badge, button, card, checkbox, dialog, drawer, dropdown-menu, field, hint, input, input-group, label, popover, select, separator, sheet, sidebar, skeleton, sonner, switch, table, tabs, textarea, tooltip, typography). Importe sempre por `@/components/ui/<nome>`. Para adicionar outro componente do shadcn, use `npx shadcn@latest add <nome>` e não edite o código gerado sem necessidade.
- **Classes:** use `cn` de `@/lib/utils` (re-exporta o pacote oficial `cn` do shadcn). Ícones: `lucide-react`. Toasts: `sonner` via `<Toaster />` do layout.
- **Tema:** `ThemeProvider` (next-themes, classe `dark`, padrão `system`) e `ThemeToggle` no layout. Os tokens ficam em `src/app/globals.css`; a marca mistura rosa (`--brand`, `--primary`, fêmea) e azul (`--sky`, macho), como nos degradês de rosa para azul; o verde fica só em `--success`. Indicadores de sexo usam azul para macho e rosa para fêmea. Prefira tokens (`text-brand`, `bg-primary`, `text-muted-foreground`) a cores fixas.
- **Hooks e utilitários:** `@/hooks/use-mobile` (usado pelo sidebar) e `@/lib/utils`.
- **Formatação:** `.prettierrc.json` com `printWidth` 200 e `trailingComma` `all`.
- **Conferência:** `npm run type-check`, `npm run lint` e `npm run build` devem passar antes de concluir.
- A API do backend fica em `/api/v1` (veja `backend/AGENTS.md`). Atualize esta seção quando entrarem novas telas, rotas ou convenções.

### Chamadas à API (BFF)

Arquitetura **BFF**, igual ao astleta: o navegador nunca fala com o Laravel nem vê o token.

- **Variável de ambiente:** `API_URL` (só no servidor), por exemplo `http://localhost:8000/api/v1` (veja `.env.example`). O `.env.local` não vai para o Git.
- **Componentes de cliente:** use `api<T>()` (`src/lib/api/client.ts`) para dados em objeto e `apiPage<T>()` para listas paginadas. Eles chamam `/bff/*` no próprio Next e devolvem o `data` do envelope `{ status, message, data }` ou lançam `ApiError` (`status`, `message`, `fieldErrors`, `firstError(campo)`, `isValidation`, `isUnauthenticated`).
- **Servidor (Server Components e rotas):** use `apiServer<T>()` / `apiServerPage<T>()` (`src/lib/api/server.ts`), que injetam o `Authorization: Bearer` a partir do cookie.
- **Sessão:** o token fica no cookie `fm_token` (`httpOnly`, `sameSite=lax`, `secure` em produção, 7 dias), gerido em `src/lib/auth/session.ts`. `/bff/auth/enter` faz o login e grava o cookie; `/bff/auth/logout` revoga o token e limpa o cookie; `/bff/[...path]` é o proxy do resto (injeta o Bearer, força `Accept: application/json`, limpa o cookie num 401) e **bloqueia `auth/login`**, para o token nunca chegar ao navegador.
- **Usuário logado:** `getCurrentUser()` (com `cache` do React, uma chamada por requisição) e `requireUser(signInPath, returnTo)`, que redireciona para `/entrar?redirect=...`. O `?redirect=` só aceita caminhos internos permitidos (`resolveRedirect`).
- **Cache Components:** o projeto usa `cacheComponents: true`. Todo acesso à API ou aos cookies precisa ficar dentro de um `<Suspense>` (com `fallback`), senão o build falha com "uncached data during prerendering".
- **Telas de autenticação** (`src/features/auth`): URLs em português (`/entrar`, `/cadastro`, `/verificar-email`, `/boas-vindas`), pastas e código em inglês. Cadastro (`nome`, `e-mail`, `senha` com as mesmas regras do backend) leva à verificação por código de 6 dígitos; o login de e-mail não verificado volta para `/verificar-email`. `/boas-vindas` é a primeira tela logada (saudação, atalhos para todas as áreas e contadores reais de pets, matches, interesses e ONGs do usuário, vindos de `welcomeServer.ts`). O componente `CredentialsForm`, `AuthShell` e o resto de `components/auth` são compartilhados.
- **Testes:** `npm test` (Vitest, `src/**/*.test.ts`) cobre `lib/api`, `lib/auth` e as validações de cada feature. Teste integrado manual: suba o backend com um banco isolado (`DB_DATABASE=/tmp/x.sqlite php artisan migrate`, `php -S 127.0.0.1:8765 -t public public/index.php`) e o front com `API_URL=http://localhost:8765/api/v1`; o código de verificação sai da tabela `email_verification_codes` (o e-mail local usa o driver `log`).

### Meus pets (`/meus-pets`)

Primeira tela de CRUD do front, em `src/features/pet` (pasta em inglês, URL em português). Serve de modelo para as próximas listas.

- **Fluxo:** a página (Server Component dentro de `<Suspense>`) lê os filtros da URL com `parsePetFilters`, busca a página com `apiServerPage` (`server/petServer.ts`) e os catálogos (`selects/species`, `sizes`, `goals`) e renderiza `PetsManager` (cliente). O cliente só dispara mutações (`services/petService.ts`, via BFF) e chama `router.refresh()`; a lista nunca é refeita no cliente.
- **Filtros na URL:** `?search=`, `?species=`, `?status=active|inactive` e `?page=`, sempre com 12 por página, mais novos primeiro. A busca tem debounce de 400 ms e qualquer filtro volta para a página 1. Se a página pedida ficou vazia (por exemplo, depois de excluir o último item dela), `PetsView` redireciona para a última página. Os filtros viram a query da API em `petFiltersToQuery` (`filters[species_id]`, `filters[is_active]`).
- **Formulário (`PetFormDialog`):** o mesmo diálogo cria e edita. Campos: nome, sexo, espécie, raça (depende da espécie e é carregada em `selects/breeds?species_id=`; trocar a espécie limpa a raça), porte, objetivo, nascimento, bio e as chaves vacinado, castrado e ativo. As validações do cliente espelham o backend (`validators/petValidators.ts`) e os erros por campo da API (`ApiError.fieldErrors`) são mapeados de volta para os campos. A API devolve `birth_date` como `dd/mm/aaaa`, mas recebe e o `<input type="date">` usa ISO: a conversão fica em `utils/petDates.ts`. Opcionais vazios vão como `null`, o que permite limpar o campo.
- **Fotos (`/meus-pets/{uuid}/fotos`, `PetPhotosManager`):** ficam em uma rota própria, acessada pelo botão "Fotos" no card do pet. Envio `multipart` (campo `photo`) pelo BFF, no máximo 10 por pet, JPG/PNG/WEBP até 5 MB (checado no cliente e na API). A foto principal é a primeira ou a escolhida; ao excluir a principal o backend promove a próxima, então a lista é sempre recarregada de `pets/{uuid}/photos` depois de cada alteração.
- **Imagens:** as URLs vêm do disco `public` do Laravel (`APP_URL/storage/...`, exige `php artisan storage:link`). Usam `next/image` com `unoptimized`, já que o host da API muda por ambiente.
- **Exclusão:** `components/DeleteDialog.tsx` (portado do astleta) pede confirmação e dispara a exclusão (soft delete no backend).
- **Área logada:** `components/app/AppShell.tsx` (atalho para o `SiteShell`, com o cabeçalho único) envolve as telas protegidas. Cada tela nova entra em `APP_LINKS` do `MainNav` e o caminho dela entra em `areaPrefix` do `EnterForm`, para o `?redirect=` funcionar.
- **Testes:** `features/pet/tests` cobre filtros e URL, datas, exibição (idade, rótulos, foto principal) e validações/payload.
- **Detalhes do pet:** veja a seção abaixo. O card da lista leva para `/meus-pets/{uuid}`.

- **Raça:** com mais de 600 raças, o formulário usa `components/SearchSelect` (popover com busca, 300 ms de debounce, descarta resposta antiga) sobre `GET selects/breeds?species_id&search`, que devolve no máximo 20 itens. O nome da raça escolhida fica no estado do formulário (`breedName`), já que a lista não traz todas. Trocar a espécie limpa a raça. O componente é genérico: serve para qualquer seleção buscada no backend.

### Detalhes do pet (`/meus-pets/{uuid}`)

Segunda tela de CRUD, em `src/features/pet-detail`. Uma página com cabeçalho do pet e quatro abas controladas pela URL (`?aba=`), no mesmo desenho de Meus pets: o servidor lê a URL, busca só os dados da aba ativa (cada aba dentro do próprio `<Suspense>`) e o cliente dispara as mutações e chama `router.refresh()`.

- **Abas e parâmetros:** `detalhes` (padrão, sem `aba` na URL), `vacinas`, `saude` e `pesos`, mais `?page=` (10 por página) e `?tipo=` (filtro por tipo, só em saúde). Parse e montagem em `utils/petDetailParams.ts` (`parsePetDetailParams`, `petDetailHref`). O UUID inválido ou de pet que não é do usuário cai em `notFound()` (a API responde 404 e `app/meus-pets/[uuid]/not-found.tsx` mostra a mensagem). Página vazia depois de excluir redireciona para a última página.
- **Detalhes:** resumo do pet (`PetAbout`) e a ficha 1:1 (`PetDetailForm`, `PUT pets/{uuid}/detail` faz criar ou atualizar). Tudo opcional; campo vazio vai como `null` para poder limpar. O formulário remonta (`key` = `updated_at`) depois de salvar.
- **Vacinas:** tabela com a próxima dose destacada por `DueBadge` (vencida, vence em até 30 dias ou em dia, calculado em `utils/petDetailDisplay.ts`). A próxima dose precisa ser depois da aplicação; datas não podem ser futuras.
- **Saúde:** o tipo vem de `selects/health-record-types`; filtro por tipo na URL; retorno também usa `DueBadge`; custo em reais (`formatBrl`), digitado com vírgula.
- **Pesos e medidas:** cartão com o último peso e a variação desde a medição anterior, e tabela com a variação linha a linha (`weightTrend`). Altura e largura opcionais.
- **Reuso:** `components/FormFields.tsx` (`TextField`, `TextAreaField`, `DateField`), `RecordFormDialog` (casca do diálogo), `RowActions`, `DeleteDialog` e o hook `hooks/use-record-dialogs.ts`, que guarda o estado do diálogo de criar, editar e excluir (o `key` remonta o formulário a cada abertura). Cada diálogo de formulário tem o seu validador em `validators/petDetailValidators.ts` e mapeia os erros por campo da API com `lib/api/formErrors.ts` (`fieldErrorsFrom`).
- **Formatos:** a API devolve datas como `dd/mm/aaaa` e decimais como string (`"12.50"`); o `<input type="date">` e a API de escrita usam ISO. A conversão fica em `lib/dates.ts` (`brToIso`, `todayIso`, `daysBetween`) e `lib/numbers.ts` (`parseDecimal` aceita vírgula ou ponto, `formatDecimal`, `formatBrl`, `toDecimalInput`).
- **Visual:** cabeçalho em degradê, abas em pílulas coloridas (tokens `sun`, `sky`, `mint`, `grape` em `globals.css`) e listas de vacinas, saúde e pesos em cards (`RecordCard`, grade de 2 colunas no desktop, faixa lateral por tipo, estatísticas e ações de editar/excluir) no lugar de tabelas.
- **Testes:** `features/pet-detail/tests` (parâmetros da URL, vencimentos e variação de peso, validadores e payloads), `lib/tests` (datas, números, erros de campo).

### Match (`/match`)

Tela de swipe entre pets, em `src/features/match`. Usa `GET pets/{uuid}/feed` e `POST pets/{uuid}/swipes`. A lista de matches e o chat estão em `/matches`.

- **Quem dá match:** o servidor busca os pets do usuário (`pets?filters[is_active]=1`) e tira os de adoção (objetivo `adoption`, achado em `selects/goals`). O pet atual vem de `?pet=`; sem ele ou se inválido, usa o primeiro. Sem nenhum pet elegível aparece `MatchEmpty` com o atalho para Meus pets. Com mais de um pet, a barra de filtros ganha o seletor "Dar match como".
- **Filtros na URL:** `?pet=`, `?especie=`, `?porte=`, `?sexo=male|female` e `?dist=` (10, 25, 50, 100 ou 300 km). Sem filtro, a API usa a mesma espécie e o mesmo objetivo do pet. `dist` exige localização no pet (ou no perfil, que vira o padrão do pet): sem ela a API responde 422 e a tela mostra a mensagem com o atalho "Remover filtro de distância". Mudar qualquer filtro muda a `key` do `MatchBoard` e reinicia o baralho (`matchBoardKey`).
- **Baralho:** `utils/deck.ts` é um reducer puro (`createDeck`, `deckReducer`, `needsRefill`, `isDeckFinished`) e `hooks/useSwipeDeck.ts` o liga à API. O servidor entrega a primeira página de 10; quando restam 3 ou menos cartões o hook busca de novo a **página 1** (e não a 2), porque o feed já exclui quem foi deslizado, e filtra os já vistos. Swipes em andamento são contados (`inFlight`): se a busca não traz ninguém novo enquanto ainda há envios pendentes, tenta de novo até 5 vezes antes de marcar o fim. O cartão sai da pilha na hora; se o envio falha (que não seja 422) ele volta ao topo e aparece um aviso; 422 (por exemplo, "já deu swipe") só avisa.
- **Gesto:** `SwipeCard` usa Pointer Events, sem biblioteca. Direita curte, esquerda passa e para cima super curte (limites 100 px e 120 px em `utils/swipeGesture.ts`), com rotação, carimbos (Curtir, Passar, Super) e animação de saída de 280 ms. Um toque nos lados da foto troca de foto (`tapZone`, `stepPhoto`), e as setas do teclado fazem o mesmo que os botões (ignoradas dentro de campos e diálogos). Os botões usam o mesmo caminho do gesto (`SwipeCardHandle.swipe`).
- **É um match:** quando o swipe responde `matched: true`, `MatchDialog` mostra a foto do pet do usuário e a do outro. A resposta do swipe traz o `match` (uuid e `other_pet`).
- **Privacidade:** o feed nunca traz coordenadas nem dono; só `distance_km` arredondado.
- **Testes:** `features/match/tests` cobre gesto, baralho, filtros da URL e rótulos; `lib/tests/uuid.test.ts` cobre o validador de UUID (movido para `lib/uuid.ts`).

### Matches e chat (`/matches`, `/matches/{uuid}`)

Em `src/features/chat`. API: `GET matches`, `GET matches/{uuid}`, `DELETE matches/{uuid}`, `GET/POST matches/{uuid}/messages` e `PUT matches/{uuid}/messages/read`.

- **Lista:** `?pet=` filtra por pet (seletor só com mais de um pet) e `?page=` pagina (12 por página, mais recentes primeiro; página além da última redireciona para a última). Cada linha mostra foto do outro pet, nome, prévia da última mensagem ("Você: ..." quando é minha), horário e selo de não lidas.
- **Datas:** a API manda `dd/mm/aaaa HH:mm:ss` em UTC. `lib/dateTime.ts` lê como UTC e `LocalTime` exibe no fuso do navegador só depois de montar (`useIsClient`), para não quebrar a hidratação.
- **Sala:** `useChat` guarda as mensagens do mais antigo ao mais novo (a API devolve o inverso, 30 por página), faz polling da página 1 a cada 5 s com a aba visível (sem websocket), marca como lidas ao receber (`PUT .../read`) e envia de forma otimista: a mensagem entra como pendente (`pending-N`), vira a confirmada ao responder e some com aviso se falhar (429 tem texto próprio). "Carregar mensagens anteriores" busca as páginas seguintes mantendo a posição da rolagem. A lista só rola para o fim se o usuário já estava perto dele.
- **Envio:** Enter envia, Shift+Enter quebra linha, limite de 2000 caracteres com contador a partir de 200 restantes. O menu do cabeçalho tem "Desfazer match" (`DeleteDialog`), que volta para `/matches`. Match inexistente ou desfeito cai em `not-found`.
- **Testes:** `features/chat/tests` (mesclagem, pendentes, agrupamento por dia, leitura, parâmetros da URL) e `lib/tests/dateTime.test.ts`.

### ONGs e Novidades (`/ongs`, `/ongs/{uuid}`, `/posts`, `/posts/{uuid}`)

Parte pública (somente leitura, sem login) em `src/features/ong`. API: `GET ongs`, `ongs/{uuid}`, `ongs/{uuid}/pets`, `ongs/{uuid}/posts`, `posts`, `posts/{uuid}`. O painel da ONG e a adoção estão nas seções seguintes (veja também "Regra de acesso" no fim).

- **Lista de ONGs:** `?search=` (debounce de 400 ms em `ListSearch`) e `?page=`, 12 por página, em ordem alfabética; só ONGs verificadas (a API já filtra). Cada card mostra logo, nome, selo "Verificada", trecho da descrição, cidade/UF e a contagem `adoptable_pets_count`.
- **Perfil da ONG:** `OngHeader` (logo, descrição, local, e-mail, telefone, Pix/doação) e abas por `?aba=` (`pets` é o padrão e não aparece na URL; `caixinhas` e `posts`), com `?page=`. Pets para adoção usam `AdoptablePetCard` (mesmo tipo `FeedPet` do match); posts usam `PostCard`. UUID inválido ou 404 da API cai em `not-found` (`NotFoundState`).
- **Novidades (`/posts`):** feed paginado (9 por página, mais recentes primeiro) com busca pelo título; `showOng` mostra a ONG (ou "Focimor" nos posts do sistema). O detalhe mostra capa, texto e link para a ONG. A data exibida é a parte `dd/mm/aaaa` do `published_at`.
- **Reuso:** `parseListParams`, `listHref`, `ongHref`, `postHref` em `utils/ongParams.ts`; `EmptyState`, `ListSkeleton`, `OngLogo`. o menu ganhou "ONGs" e "Novidades" e `EnterForm` inclui `/ongs` e `/posts` em `areaPrefix`.
- **Testes:** `features/ong/tests/ongParams.test.ts` (parâmetros, hrefs, query e formatação).

### Adoção (`/adocao`, `/adocao/{uuid}`, `/adocao/descobrir`, `/adocoes`, `/adocoes/{uuid}`)

Em `src/features/adoption`. API: `GET adoptions/feed`, `POST adoptions/swipes`, `GET adoptions` (meus interesses), `GET adoptions/received`, `GET/DELETE adoptions/{uuid}`, `PUT adoptions/{uuid}/decision` e `GET/POST adoptions/{uuid}/messages` (+ `PUT .../read`).

- **Swipe (`/adocao/descobrir`, exige login):** reaproveita o baralho do Match (`useSwipeDeck`, `SwipeCard`, `PetCardFace`, `SwipeActions`) com `AdoptionBoard`. Filtros na URL são os mesmos do match (`?especie=`, `?porte=`, `?sexo=`, `?dist=`) pelo `MatchFiltersBar` com `basePath="/adocao/descobrir"`; não há seletor de pet. `dist` usa a localização do perfil (sem ela a API responde 422 e a tela mostra a mensagem). Passar não cria nada; curtir ou super curtir envia o interesse (cria a adoção pendente) e mostra um toast com o atalho "Ver". O cartão mostra o nome da ONG (`FeedPet.ong`).
- **Lista (`/adocoes`):** abas `?aba=` (`meus` padrão e `recebidos`), `?status=` (pending, accepted, declined, adopted, cancelled) e `?page=`, 12 por página. Cada linha mostra o pet, a outra parte (ONG, ou "Fulano quer adotar" nos recebidos), a situação, a prévia da última mensagem e o selo de não lidas.
- **Detalhe (`/adocoes/{uuid}`):** resumo, ações por papel (`availableActions`) e chat. Dono ou membro da ONG: aceitar, recusar e, depois de aceito, marcar como adotado (recusa os outros interessados). Quem demonstrou interesse: cancelar. Recusar, cancelar e adotar pedem confirmação (`DeleteDialog`).
- **Chat no mobile:** em `/adocoes/{uuid}` o chat vira tela cheia no celular: `AdoptionChatPanel` mostra o botão "Abrir conversa com ..." e abre o `ChatRoom` em overlay (`fixed inset-0`, trava a rolagem); a seta do `AdoptionChatHeader` fecha o overlay (no desktop continua inline e a seta volta para Adoções).
- **Chat de adoção:** reusa `ChatRoom` com `scope="adoptions"` (o `useChat` e o `chatServiceFor` recebem o escopo). `chatAccess`: pendente, recusado e cancelado não têm chat; aceito abre para enviar; adotado fica só leitura (`canSend={false}`), como no backend.
- **Testes:** `features/adoption/tests/adoption.test.ts` (parâmetros, ações por papel, acesso ao chat, rótulos).

### Painel da ONG (`/minhas-ongs`, `/minhas-ongs/{uuid}`)

Em `src/features/ong-panel`. API: `GET my-ongs` (lista simples, sem paginação), `GET/PUT my-ongs/{uuid}`, `POST .../logo`, `GET/POST .../members`, `PUT/DELETE .../members/{userId}`, `GET/POST .../pets`, `GET/POST .../posts`, `PUT/DELETE .../posts/{post}` e `POST .../posts/{post}/cover`.

- **Lista:** `/minhas-ongs` mostra as ONGs do usuário (papel e situação). Com uma só ONG redireciona direto para o painel; sem nenhuma mostra um estado vazio. 404 ou 403 da API cai em `not-found`.
- **Painel:** cabeçalho (logo, situação, papel, link para a página pública) e abas por `?aba=` (`dados` é o padrão e não aparece na URL, `pets`, `posts`, `membros`) e `?page=` (12 por página). Cada aba busca só os dados dela no servidor; os formulários mutam pelo BFF e chamam `router.refresh()`.
- **Permissões (espelham o backend):** só administrador edita dados, logo e adiciona/altera membros; qualquer membro pode sair da ONG, e o admin remove os outros (a ONG precisa de pelo menos um admin; o backend recusa e a mensagem aparece no formulário). Cadastrar pets e posts exige ONG verificada (`status = verified`); sem isso os botões ficam desativados e o painel avisa.
- **Dados:** `ProfilePanel` (descrição, contato, endereço, UF, latitude/longitude, Pix, como ajudar) com `validators/panelValidators.ts`; vazio vai como `null`. O botão "Usar minha localização" preenche latitude e longitude com a geolocalização do navegador (pede permissão). Coordenadas aceitam negativos e vírgula (`parseCoordinate`; o `parseDecimal` global não aceita sinal). Logo: botão "Alterar imagem de perfil" no cabeçalho do painel (`LogoUploadButton`, só admin), ao lado de "Ver página pública" (empilhados no celular); JPG/PNG/WEBP até 2 MB, envio `multipart` (`logo`).
- **Pets:** lista os pets da ONG e cria pelo `PetFormDialog` com a prop `ongUuid` (sem o campo de objetivo; o backend força `adoption` e ativo). Cada card tem "Ver detalhes" (`/meus-pets/{uuid}`, a mesma página de detalhes do pet, com ficha, vacinas, saúde e pesos) e "Fotos" (`/meus-pets/{uuid}/fotos`); o backend (`pets/{pet}`, escopo `managedBy`) libera os dois para qualquer membro da ONG. O "Voltar" é o do `SiteShell`, que volta no histórico (para o painel, vindo dele).
- **Posts:** o título leva ao detalhe `/minhas-ongs/{uuid}/posts/{post}` (`PostDetailView`, `GET my-ongs/{ong}/posts/{post}`, vale para rascunho e agendado): capa, situação, texto, Editar, Excluir (volta à aba) e link para a página pública quando publicado. Lista com situação (`publishState`: publicado, agendado, rascunho), criar/editar em `PostFormDialog` (título, conteúdo, data de publicação opcional, capa até 5 MB enviada em seguida por `.../cover`) e excluir com confirmação.
- **Membros:** adiciona por e-mail (a conta precisa existir), troca o papel e remove.
- **Testes:** `features/ong-panel/tests/panel.test.ts` (parâmetros, permissões, validadores e payloads).

### Landing (`/`)

Em `src/features/landing`, montada em `app/page.tsx`. Seções (cada uma um componente): o cabeçalho é o `SiteHeader` compartilhado com as páginas públicas (veja "Regra de acesso"), `HeroSection` (título, CTAs e dois cartões de swipe com fotos reais), `GallerySection` (faixa de fotos de cães e gatos, marcada como ilustrativa), `FeaturesSection` (6 recursos: match, chat, adoção, ONGs verificadas, ficha de saúde e painel da ONG, cada um com uma foto de cachorro no lugar do ícone), `HowItWorksSection` (fluxo de tutores e de adoção, cada um com foto), `OngSection` (painel e verificação; o cadastro de ONG é feito pela equipe), `ShowcaseSection` (três fileiras de cards com rolagem horizontal, no estilo das fileiras Arenas, Rachas e Torneios do astleta: Adoção, ONGs e Novidades, cada uma com "Ver todos"; dados reais de `adoptable-pets`, `ongs` e `posts` via `apiPublicPage` com revalidação de 5 minutos; fileira vazia some e, sem nenhum dado, a seção inteira; a idade do pet vem de `PetDetailsLine`, componente de cliente que só calcula depois de montar; `new Date()` em componente de servidor aí quebra o prerender do `cacheComponents`), `FinalCta` com rodapé. Os botões que dependem do login (`getCurrentUser`) ficam em `<Suspense>`, por causa do `cacheComponents`. Não há números, depoimentos nem preços inventados; o texto só promete o que o sistema faz. Ao criar uma tela nova relevante, atualize os recursos aqui.

As fotos da landing ficam em `features/landing/utils/landingImages.ts` (links públicos de dog.ceo e TheCatAPI, os mesmos do seed; `PetPhoto` usa `next/image` com `unoptimized`). São ilustrativas e dependem dos hosts externos; para produção, troque por arquivos próprios em `public/` ou S3 com licença conhecida.

### Regra de acesso: o que é público e o que exige login

- **Público (sem login):** landing `/`, `/ongs`, `/ongs/{uuid}`, `/posts`, `/posts/{uuid}`, `/adocao` (lista com busca e filtros por espécie, porte e sexo) e `/adocao/{uuid}` (página do pet com galeria, ficha e a ONG). Quem doa ou adota precisa ver tudo isso sem conta. Os dados vêm das rotas públicas do backend (`ongs`, `posts`, `adoptable-pets`, `selects/*`) por `apiPublic`/`apiPublicPage`, com revalidação de 60 s (catálogos 1 h). Não use `requireUser` nessas páginas.
- **Exige login:** só as ações do usuário e o painel: `/adocao/descobrir` (swipe, usa o feed personalizado), `/adocoes` e `/adocoes/{uuid}` (interesses e chat), `/minhas-ongs` (painel da ONG, só membros), `/meus-pets`, `/match`, `/matches`. O único ponto de login dentro de uma página pública é o botão "Quero adotar" (`AdoptButton`): deslogado ele vira "Entrar para adotar" com `?redirect=` de volta ao pet; logado envia o interesse (`POST adoptions/swipes`, `like`, `direct: true`) e abre `/adocoes/{uuid}`. O "Quero adotar" prevalece sobre o deslizar: se o usuário já tinha passado no pet, a ação vira curtida e o interesse é criado; se já havia contato (pendente, aceito ou adotado) a API responde 422 "Você já entrou em contato…"; interesse cancelado é reaberto e interesse recusado pelo responsável continua bloqueado.
- **Cabeçalho único:** a landing, as páginas públicas e as páginas de usuário usam o mesmo `SiteHeader` (estilo do astleta: logo à esquerda, menu centralizado com o ícone sobre o texto e sublinhado no item ativo, ações à direita; no celular o menu vira uma segunda linha). O `MainNav` escolhe os links: deslogado, Início, Adoção, ONGs e Novidades (e à direita Cadastre sua ONG, tema, Entrar e Criar conta); logado, Início (`/boas-vindas`), Meus pets, Adoção, ONGs, Minha ONG e Novidades (e à direita tema e o menu da conta, `UserAccountMenu`: um dropdown com nome, e-mail, atalhos para as áreas do usuário e de exploração e o "Sair"; não há mais botão Sair solto). `SiteShell` (cabeçalho + conteúdo) serve às páginas públicas, que descobrem a sessão sozinhas dentro de um `<Suspense>`, e às de usuário, que passam o `user` (`AppShell` é só um atalho para `SiteShell user=...`). Nunca crie outro cabeçalho nem menu separado.
- **Loader entre páginas:** `components/app/NavigationProgress.tsx` (no `layout.tsx`) mostra uma barra rosa-azul no topo ao clicar em links internos até a rota mudar. Trocas por `router.push` não disparam.
- **Voltar:** `components/app/BackButton.tsx` entra no `SiteShell` (topo do conteúdo, some em `/` e `/boas-vindas`) e volta no histórico, ou para `/boas-vindas` se não houver.
- **SEO das páginas públicas:** `lib/seo.ts` (`pageMetadata`, `clip`, `siteUrl`, `absoluteUrl`) monta título, descrição, canonical, Open Graph e Twitter; toda página pública usa `pageMetadata` e as de detalhe (`/ongs/{uuid}`, `/posts/{uuid}`, `/adocao/{uuid}`) usam `generateMetadata` com os dados da API (imagem = logo, capa ou foto do pet). A URL do site vem de `NEXT_PUBLIC_SITE_URL` (defina em produção; `metadataBase` no layout). `app/sitemap.ts` lista as páginas fixas e até 100 pets, ONGs e posts; `app/robots.ts` bloqueia `/bff` e as áreas logadas. JSON-LD (`components/JsonLd.tsx`): WebSite e Organization na landing, NGO na ONG e Article no post. As imagens do Open Graph vêm de URLs assinadas do S3 (7 dias). Páginas novas e públicas devem definir `pageMetadata`.
- **Tooltip em botão só com ícone:** o `Button` (`components/ui/button.tsx`) envolve sozinho num `Tooltip` todo botão `size="icon*"` (ou `InputGroupButton` com ícone) que tenha `aria-label`, e o texto do tooltip é o próprio `aria-label`. Em botão só com ícone, sempre defina `aria-label`; não envolva com `Hint` (duplica).
- **Rodapé único:** `components/app/SiteFooter.tsx` fica no `app/layout.tsx`, então aparece em todas as telas (fundo sólido branco ou preto). Não crie outro rodapé.
- **Idade do pet:** nunca calcule com `new Date()` em componente de servidor; use `PetDetailsLine` (cliente, calcula depois de montar). Só pets de ONGs verificadas aparecem na adoção pública.
- **Testes:** `features/adoption/tests/adoptablePetParams.test.ts`.

- **Cor por sexo:** use `genderTheme(gender)` (`features/pet/utils/petDisplay.ts`) em vez de ternários: macho é azul (`--sky`) e fêmea é rosa (`--brand`). Ele devolve as classes `text`, `soft`, `solid`, `border`, `hoverBorder` e `gradient`. A página do pet (`/adocao/{uuid}`) pinta nome, moldura da foto, fichas, botão "Quero adotar" e link da ONG com o tema do sexo; os cards de adoção e o selo do detalhe do pet seguem a mesma regra.

### Caixinhas das ONGs (campanhas de arrecadação)

Inspiradas na Caixinha do astleta: cada caixinha tem título, descrição, meta, valor arrecadado e prazo opcional, com barra de progresso (rosa para azul). O sistema só registra o progresso; quem doa usa o Pix e os contatos da ONG.

- **Pública:** aba "Caixinhas" (`?aba=caixinhas`) em `/ongs/{uuid}`, ao lado de "Para adoção" e "Novidades", com paginação (6 por página) e os cards `CampaignCard` (título, arrecadado em verde, descrição, `CampaignProgress`, situação e prazo). Vem de `ongs/{uuid}/campaigns` (as canceladas não aparecem; sem caixinhas a aba mostra um estado vazio). Ficam em `features/ong` (`types`, `utils/campaignDisplay.ts`, `components/Campaign*.tsx` com `CampaignsList`, `fetchOngCampaigns`).
- **Painel:** aba "Caixinhas" (`?aba=caixinhas`) em `/minhas-ongs/{uuid}`, com `CampaignsPanel`: criar e editar em `CampaignFormDialog` (título, descrição, meta, arrecadado, prazo e, na edição, "cancelada"), excluir com confirmação e "Registrar doação" (`DonationDialog`), que soma um valor ao arrecadado. A situação é calculada no cliente (`resolveCampaignStatus`: meta batida, em andamento ou cancelada) e enviada junto. Exige ONG verificada, como pets e posts.
- **Valores:** a API devolve decimais como string (`"250.00"`); o formulário usa vírgula (`parseDecimal`, `toDecimalInput`, `formatBrl`).
- **Testes:** `features/ong-panel/tests/campaign.test.ts`.

### Pré-cadastro de ONG (`/ongs/cadastro`, `/ongs/cadastro/status`)

Em `src/features/ong-register`, no mesmo desenho do pré-cadastro de empresa do astleta (`AuthShell` com hero de passos e cartão com o formulário). Público e sem login.

- **Formulário (`RegisterForm`):** nome da ONG, e-mail (use o da conta que vai administrar), telefone com máscara, CNPJ opcional (máscara e dígitos verificadores em `lib/masks.ts`), cidade, UF, texto sobre a ONG e aceite da LGPD. Validação em `validators/ongRegisterValidators.ts` espelhando o backend; erros por campo da API voltam pelo `fieldErrorsFrom`; 429 mostra aviso de muitas tentativas. Envia `POST ong-registrations` pelo BFF.
- **Depois do envio (`RegisterPanel`):** mensagem de sucesso, atalho para acompanhar e para criar a conta (o painel só aparece para quem tem conta com o e-mail do pré-cadastro).
- **Acompanhar (`StatusLookup`):** consulta por e-mail ou telefone em `GET ong-registrations/status` e mostra a situação (Recebido, Em contato, Aprovado, Não aprovado; `utils/statusInfo.ts`); aprovado leva a `/minhas-ongs`.
- **Fluxo:** a equipe aprova no admin ("Aprovar e criar ONG"), que cria a ONG verificada e torna admin o usuário com o mesmo e-mail. Pontos de entrada: botão "Cadastre sua ONG" no `SiteHeader` (telas grandes), seção "Para ONGs" da landing, `/ongs` e o estado vazio de `/minhas-ongs`.
- **Testes:** `features/ong-register/tests/ongRegister.test.ts`.

### Página 404

`app/not-found.tsx` é a 404 global (qualquer endereço inexistente), dentro do cabeçalho único (`SiteShell`, com o menu certo para quem está ou não logado) e com os atalhos "Voltar para o início" e "Ver pets para adoção". Os `not-found.tsx` de cada rota (`/ongs/{uuid}`, `/posts/{uuid}`, `/adocao/{uuid}`, `/adocoes/{uuid}`, `/minhas-ongs/{uuid}`) usam o mesmo `NotFoundState` (`features/ong/components`), que aceita um botão secundário opcional. Crie novos `not-found` com ele, não com tela cheia própria.
