# PI5 Viveiro Reflorestamento

Projeto do PI para acompanhar dados de sensores em um viveiro de mudas.

A ideia é simples: a API guarda os dispositivos e as leituras dos sensores, e o painel web mostra essas informações de um jeito mais fácil de acompanhar.

## Pastas

```txt
api/  backend em Laravel
web/  frontend em Vue + Vite
```

## Como rodar

Primeiro suba a API:

```sh
cd api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Depois suba o front:

```sh
cd web
cp .env.example .env
npm install
npm run dev
```

No `.env` do front, a URL da API precisa ficar assim quando estiver rodando local:

```sh
VITE_API_URL=http://localhost:8000/api
```

## Deploy

O plano é:

- `web/` no Vercel
- `api/` no Railway

No Vercel, o diretório raiz do projeto deve ser `web`.

No Railway, o diretório raiz deve ser `api`.

Depois que a API estiver publicada, coloque a URL dela no Vercel:

```txt
VITE_API_URL=https://url-da-api-no-railway/api
```

E no Railway coloque a URL do front:

```txt
FRONTEND_URL=https://url-do-front-no-vercel
```

## Antes de enviar alterações

No front:

```sh
cd web
npm run build
npm run lint
```

Na API:

```sh
cd api
php artisan test
```

