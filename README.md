# Teste Técnico — Desenvolvedor Full Stack PHP / Laravel

Este repositório contém uma aplicação de e‑commerce simples construída com Laravel 12, Livewire v3, Blade e Tailwind CSS. O objetivo do projeto é demonstrar conhecimentos em PHP, Laravel, Livewire, policies, Jobs, Mailables, testes e boas práticas.

### Pré-requisitos

-   PHP >= 8.4
-   Laravel 12
-   Composer
-   Banco de dados MySQL

### Instalação (local)

1. Clone o repositório

```bash
git clone git@github.com:jonas-amilton/catalogo.git
cd catalogo
```

2. Instalar dependencias

```bash
composer install
npm install
```

3. Copie o arquivo de ambiente e gere a APP_KEY

```bash
cp .env.example .env
php artisan key:generate
```

4. Configure as variáveis no .env

```bash
APP_NAME="Teste"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=teste
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database

MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

5. Execute migrations e seeders

```bash
php artisan migrate --seed
```

6. Gere assets do vite

```bash
npm run dev
# para produção
# npm run build
```

7. Rode a aplicação

```bash
php artisan serve
```

## Testando e‑mails (como eu testei)

No meu ambiente local como os e‑mails eram processados pelos Jobs. Para facilitar a reprodução, documentei os passos que usei:

```bash
php artisan queue:work
```

no .env
`MAIL_MAILER=log`

e no terminal
`tail -f storage/logs/laravel.log`

Observação: meu teste local consistiu em rodar php artisan queue:work e verificar que os e‑mails gerados pelos jobs aparecem no log
