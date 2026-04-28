# API Reflorestamento

API Laravel para cadastro de dispositivos e leituras dos sensores do viveiro.

## Rodando localmente

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Por padrao, a API fica disponivel em `http://localhost:8000/api`.

## Rotas principais

- `GET /api/teste`: verifica se a API esta respondendo.
- `GET /api/devices`: lista dispositivos.
- `POST /api/devices`: cria um dispositivo.
- `GET /api/readings`: lista leituras.
- `POST /api/readings`: cria uma leitura.
- `GET /api/readings/latest`: retorna a leitura mais recente usada pelo dashboard web.

## Exemplo de leitura

```json
{
  "device_code": "viveiro-a-01",
  "soil_moisture": 62.35,
  "air_temperature": 27.4,
  "air_humidity": 78.1,
  "irrigation_status": false,
  "source": "esp32",
  "collected_at": "2026-04-27T10:00:00-03:00"
}
```

## Testes

```sh
php artisan test
```
