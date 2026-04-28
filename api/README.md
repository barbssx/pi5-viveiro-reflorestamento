# API

Backend do projeto, feito em Laravel.

Aqui ficam os dispositivos do viveiro e as leituras enviadas pelos sensores.

## Rodando

```sh
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Por padrão, a API sobe em:

```txt
http://localhost:8000
```

## Enviando uma leitura

Exemplo de corpo para cadastrar uma leitura:

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

