# Web Reflorestamento

Dashboard Vue/Vite para monitoramento de sensores de um viveiro. A interface acompanha dispositivos, historico de leituras e o estado atual da irrigacao com uma visualizacao clara para operacao diaria.

Este pacote faz parte do monorepo `pi5-viveiro-reflorestamento` e deve ser implantado no Vercel com `web` como diretorio raiz.

## Funcionalidades

- Painel geral com ultimo registro, historico e dispositivos cadastrados.
- Navegacao dedicada para visao geral, ultimo registro, historico e dispositivos.
- Filtro por dispositivo, periodo e limite de leituras.
- Indicadores de media, menor umidade do solo, media de temperatura, media de umidade do ar e taxa de irrigacao ativa.
- Estados de erro para servidor indisponivel ou ausencia de dados.
- Tema escuro suave com cores de status para leitura operacional.

## Telas

```txt
Visao geral
Ultimo registro
Historico de leituras
Dispositivos cadastrados
```

## Requisitos

- Node.js `^20.19.0` ou `>=22.12.0`
- npm
- Servidor da API rodando localmente ou em uma URL acessivel

## Configuracao

Crie o `.env` do frontend:

```sh
cp .env.example .env
```

Configure a URL base do servidor de dados:

```sh
VITE_API_URL=http://localhost:8000/api
```

Em producao, use a URL publica da API publicada no Railway.

## Rodando localmente

Instale as dependencias:

```sh
npm install
```

Suba o frontend:

```sh
npm run dev
```

Abra o endereco informado pelo Vite no terminal.

## Scripts

```sh
npm run dev      # servidor de desenvolvimento
npm run build    # build de producao
npm run lint     # lint com correcoes automaticas
npm run format   # formatacao do codigo em src/
```

## Antes de subir no GitHub

Confira:

- `.env` nao deve ser versionado.
- `node_modules/`, `dist/` e caches locais devem ficar fora do repositorio.
- Rode `npm run build`.
- Rode `npm run lint`.
- Garanta que `VITE_API_URL` aponta para o servidor correto no ambiente de execucao.
