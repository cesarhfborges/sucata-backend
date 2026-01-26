# Sistema de sucata - Platoflex


## Requerimentos:

```
- PHP 8.1 ou maior
- Habilitar extensão GD no php
- Composer
```

## Rodando o sistema em DEV
- Baixe o código, usando git ou zip

1 - Instale as dependencias
```
composer install
```

2 - Crie ou copie o .env
```
cp .env.example .env
```

3 - Gere o secret key
```
php artisan key:generate
```

4 - Gere o secret key
```
php artisan key:generate
```

5 - Gere o secret key JWET
```
php artisan jwt:secret
```

6 - Configure as credenciais do Database
```
- host
- port
- database
- username
- password
```


7 - Efetue as migrações
```
php artisan migrate:fresh --seed
```

8 - Rode o servidor
```
php -S localhost:8000 -t public
```

### Comandos do framework

- Listar
```
php artisan
```

- Opções
```
Available commands:
  clear-compiled       Remove the compiled class file
  completion           Dump the shell completion script
  help                 Display help for a command
  list                 List commands
  migrate              Run the database migrations
  optimize             Optimize the framework for better performance
  serve                Serve the application on the PHP development server
  tinker               Interact with your application
 auth
  auth:clear-resets    Flush expired password reset tokens
 cache
  cache:clear          Flush the application cache
  cache:forget         Remove an item from the cache
  cache:table          Create a migration for the cache database table
 db
  db:seed              Seed the database with records
  db:wipe              Drop all tables, views, and types
 jwt
  jwt:generate-certs   Generates a new cert pair
  jwt:secret           Set the JWTAuth secret key used to sign the tokens
 key
  key:generate         Set the application key
 make
  make:cast            Create a new custom Eloquent cast class
  make:channel         Create a new channel class
  make:command         Create a new Artisan command
  make:controller      Create a new controller class
  make:event           Create a new event class
  make:exception       Create a new custom exception class
  make:factory         Create a new model factory
  make:job             Create a new job class
  make:listener        Create a new event listener class
  make:mail            Create a new email class
  make:middleware      Create a new middleware class
  make:migration       Create a new migration file
  make:model           Create a new Eloquent model class
  make:notification    Create a new notification class
  make:observer        Create a new observer class
  make:pipe            Create a new pipe class
  make:policy          Create a new policy class
  make:provider        Create a new service provider class
  make:request         Create a new form request class
  make:resource        Create a new resource
  make:rule            Create a new validation rule
  make:seeder          Create a new seeder class
  make:test            Create a new test class
 migrate
  migrate:fresh        Drop all tables and re-run all migrations
  migrate:install      Create the migration repository
  migrate:refresh      Reset and re-run all migrations
  migrate:reset        Rollback all database migrations
  migrate:rollback     Rollback the last database migration
  migrate:status       Show the status of each migration
 notifications
  notifications:table  Create a migration for the notifications table
 queue
  queue:batches-table  Create a migration for the batches database table
  queue:clear          Delete all of the jobs from the specified queue
  queue:failed         List all of the failed queue jobs
  queue:failed-table   Create a migration for the failed queue jobs database table
  queue:flush          Flush all of the failed queue jobs
  queue:forget         Delete a failed queue job
  queue:listen         Listen to a given queue
  queue:restart        Restart queue worker daemons after their current job
  queue:retry          Retry a failed queue job
  queue:table          Create a migration for the queue jobs database table
  queue:work           Start processing jobs on the queue as a daemon
 route
  route:list           Display all registered routes.
 schedule
  schedule:run         Run the scheduled commands
  schedule:work        Start the schedule worker
 schema
  schema:dump          Dump the given database schema
```

## Problemas

- limpeza de cache
```
php artisan cache:clear
```

- Otimizacao automatizada
```
php artisan optimize
```

