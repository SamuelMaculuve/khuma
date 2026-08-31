# Onboarding — iniciar o Khuma Dashboard

Este guia é o ponto de partida para quem entra no projecto, incluindo o Jair. A branch de integração é `Lead_dev`; trabalhe sempre numa branch curta criada a partir dela.

## O projecto

Khuma Dashboard é um SaaS Laravel 11 para CRM de leads/clientes, utilizadores e equipas, planos/subscrições, WhatsApp e campanhas de email. A interface usa Blade, Livewire, Tailwind, Alpine e Vite; o desenvolvimento local usa SQLite, filas e agendamentos Laravel.

## Requisitos

- Git;
- PHP 8.2+ com `bcmath`, `curl`, `dom`, `fileinfo`, `mbstring`, `openssl`, `pdo_sqlite` e `sqlite3`;
- Composer;
- Node.js LTS e npm.

Para receber email por IMAP, o PHP também precisa da extensão `imap`. Ela não está instalada no ambiente local analisado.

## Instalação local

No PowerShell, dentro da pasta do projecto:

```powershell
git switch Lead_dev
git pull --ff-only origin Lead_dev
composer install
npm ci
Copy-Item .env.example .env
New-Item -ItemType File -Path database/database.sqlite -Force
```

### Correcção temporária necessária

No estado actual, `.env.example` começa com esta linha inválida:

```text
php artisan db:seed --class=EmployeeSeeder
```

Depois de copiar o ficheiro, remova somente essa primeira linha do `.env`, antes de `APP_NAME`. A tarefa J-01 corrige isto definitivamente no repositório.

Continue com:

```powershell
php artisan key:generate
php artisan migrate --seed
npm run build
```

Nunca comite `.env`, a base SQLite local, tokens, passwords ou chaves de serviços externos.

## Executar a aplicação

Abra terminais separados para manter cada processo visível:

```powershell
php artisan serve
php artisan queue:work
php artisan schedule:work
npm run dev
```

Abra `http://127.0.0.1:8000`.

Contas locais semeadas:

| Perfil | Email | Palavra-passe |
| --- | --- | --- |
| Administrador | `admin@kuma.test` | `password` |
| Utilizador | `user@kuma.test` | `password` |

Estas contas são apenas para desenvolvimento local.

## Percurso visual recomendado

1. Entre como administrador.
2. Veja Dashboard e CRM (`/leads`); o Kanban tem os estados `new`, `contacted`, `qualified`, `proposal`, `negotiation`, `won` e `lost`.
3. Abra Email Marketing (`/email-campaigns`) para campanhas, métricas e templates.
4. Conheça Subscription, Users, Plans e Settings.
5. Não crie instâncias WhatsApp, domínios ou envios reais sem aprovação técnica.

## Mapa rápido do código

| Caminho | Para quê serve |
| --- | --- |
| `routes/web.php` | Rotas, autenticação e feature gates |
| `app/Livewire/` | Kanban, settings, campanhas e WhatsApp |
| `app/Http/Controllers/` | Fluxos HTTP, auth, leads e subscrições |
| `app/Models/` | Entidades e relações de dados |
| `app/Jobs/` | Envio de campanhas e inbound email |
| `routes/console.php` | Tarefas agendadas |
| `database/migrations/` | Evolução do esquema |
| `tests/Feature/` | Testes de comportamento e autorização |

`company_id` é a fronteira de dados entre empresas. Todo endpoint, query, componente Livewire ou job deve confirmar a empresa do utilizador no servidor; não basta confiar num ID enviado pelo browser.

## Antes de abrir um PR

```powershell
php artisan about
php artisan migrate:status
php artisan test
npm run build
git diff --check
```

No estado analisado, `phpunit.xml` ainda não força uma base exclusiva de testes. Não rode testes contra uma base de desenvolvimento partilhada; a J-01 trata o isolamento. Há também falhas conhecidas em `RegistrationTest` e `ProfileTest`, que são objecto da J-02.

## Forma de trabalhar

```powershell
git switch Lead_dev
git pull --ff-only origin Lead_dev
git switch -c feat/descricao-curta
```

- Uma tarefa por branch e commits pequenos.
- Use migrations novas; não altere migrations já partilhadas.
- Inclua testes para a regra alterada e testes de acesso negado para permissões.
- Jobs, pagamentos e webhooks devem ser seguros para repetição.
- Use `Mail::fake()` nos testes; não envie email real.
- Não copie tokens Uazapi/WhatsApp, Mailcow, Cloudflare ou M-Pesa. Há uma auditoria de segredos e integrações pendente.

## Próximo passo para o Jair

Leia [o plano de trabalho](PLANO_DE_TRABALHO_LEAD_DEV.md) e inicie a **J-01: base de desenvolvimento e testes isolados**. Ela resolve o `.env.example`, cria uma configuração segura de testes e deixa o ambiente previsível para as restantes tarefas.
