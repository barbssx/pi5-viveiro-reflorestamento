# Web

Front do painel do viveiro.

Ele mostra o último registro dos sensores, o histórico de leituras e os dispositivos cadastrados. Também dá para filtrar por período, limite de registros e dispositivo.

## Rodando

```sh
cp .env.example .env
npm install
npm run dev
```

No `.env`:

```sh
VITE_API_URL=http://localhost:8000/api
```

## Comandos úteis

```sh
npm run dev
npm run build
npm run lint
npm run format
```

## Deploy

No Vercel, use esta pasta como diretório raiz:

```txt
web
```

Depois configure `VITE_API_URL` com a URL da API publicada.

