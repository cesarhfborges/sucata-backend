# Sistema de sucata


## Requerimentos:

```
- PHP 8.1 ou maior
- Habilitar extensão GD no php
- Composer
```

## Rodando o sistema em DEV
Baixe o código, usando git ou zip

Instale as dependencias
```
composer install
```

Crie ou copie o .env
```
cp .env.example .env
```

Gere o secret key
```
php artisan key:generate
```

Gere o secret key
```
php artisan key:generate
```

Gere o secret key JWET
```
php artisan jwt:secret
```

Configure as credenciais do Database
- host
- port
- database
- username
- password


Efetue as migrações
```
php artisan migrate:fresh --seed
```

Rode o servidor
```
php -S localhost:8000 -t public
```
