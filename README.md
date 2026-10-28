# Focimor

Plataforma de adoção de pets no estilo "tinder": usuários dão swipe em pets disponíveis para adoção, fazem match com ONGs e conversam por chat. ONGs publicam pets, posts e campanhas; administradores gerenciam tudo pelo painel Filament.

## Stack

| Camada   | Tecnologias                                                                                        |
| -------- | -------------------------------------------------------------------------------------------------- |
| Backend  | PHP 8.3+, Laravel 13, Laravel Sanctum, Filament 5 (painel admin), Spatie Activitylog, Flysystem S3 |
| Frontend | Next.js 16, React 19, TypeScript, Tailwind CSS 4, shadcn/ui (Radix), Vitest                        |
| Banco    | SQLite por padrão (configurável via `.env`)                                                        |

## Estrutura

```
.
├── backend/    # API REST (Laravel) + painel admin (Filament)
└── frontend/   # Aplicação web (Next.js)
```

## Funcionalidades

- Cadastro, login, verificação de e-mail e recuperação de senha
- Perfil de usuário com avatar
- CRUD de pets (fotos, peso, vacinas, histórico de saúde, detalhes)
- Pets adotáveis, swipes e matches com mensagens
- ONGs: listagem, pets, posts e campanhas públicas
- Pré-cadastro de ONGs
- Feed de posts
- Listas auxiliares: espécies, raças, portes, objetivos e tipos de registro de saúde
- Painel administrativo (Filament) para todos os recursos

## Pré-requisitos

- PHP 8.3+ e Composer
- Node.js 20+ e npm

## Como rodar

### Backend

```bash
cd backend
composer setup        # instala deps, cria .env, gera key, roda migrations e build dos assets
composer dev          # sobe o servidor de desenvolvimento
```

API disponível em `http://localhost:8000/api/v1`.

### Frontend

```bash
cd frontend
cp .env.example .env  # ajuste se necessário
npm install
npm run dev
```

App disponível em `http://localhost:3000`.

Variáveis do frontend:

| Variável               | Padrão                         |
| ---------------------- | ------------------------------ |
| `API_URL`              | `http://localhost:8000/api/v1` |
| `NEXT_PUBLIC_SITE_URL` | `http://localhost:3000`        |

## Testes e qualidade

```bash
# backend
cd backend && composer test

# frontend
cd frontend && npm test
cd frontend && npm run lint
cd frontend && npm run type-check
```

## API

Prefixo `/api/v1`. Principais grupos de rotas:

- `auth/*` — registro, login, verificação de e-mail, reset de senha, `me`
- `ongs`, `posts`, `adoptable-pets` — leitura pública
- `selects/*` — dados para formulários (espécies, raças, portes, objetivos)
- `ong-registrations` — pré-cadastro de ONGs
- `pets` — gerenciamento dos pets do usuário (autenticado via Sanctum)

Veja `backend/routes/api.php` para a lista completa.

## Licença

Defina a licença do projeto (o backend base Laravel é MIT).
