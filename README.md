# PI5 Viveiro Reflorestamento

Monorepo do projeto de monitoramento de viveiro para reflorestamento.

## Estrutura

```txt
api/  API Laravel para dispositivos e leituras dos sensores
web/  Dashboard Vue/Vite para acompanhamento operacional
```

## Rodando localmente

### API

```sh
cd api
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Por padrao, a API fica em:

```txt
http://localhost:8000
```

### Web

```sh
cd web
cp .env.example .env
npm install
npm run dev
```

No `.env` do frontend, configure:

```sh
VITE_API_URL=http://localhost:8000/api
```

## Deploy

### Vercel

Configure o projeto apontando para:

```txt
Root Directory: web
Build Command: npm run build
Output Directory: dist
```

Variavel de ambiente:

```txt
VITE_API_URL=https://sua-api.railway.app/api
```

### Railway

Configure o servico apontando para:

```txt
Root Directory: api
Start Command: php artisan serve --host=0.0.0.0 --port=$PORT
```

Variaveis importantes:

```txt
APP_ENV=production
APP_DEBUG=false
APP_URL=https://sua-api.railway.app
FRONTEND_URL=https://seu-web.vercel.app
```

Configure tambem as variaveis de banco conforme o banco usado no Railway.

## Checklist antes do commit

```sh
cd web && npm run build && npm run lint
```

Para a API, apos instalar dependencias:

```sh
cd api && php artisan test
```

