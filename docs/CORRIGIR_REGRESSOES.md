# J-02 — Corrigir as regressões actuais de autenticação e perfil


## Objectivo

Corrigir as regressões identificadas nos testes de autenticação e perfil, garantindo que a suite de testes funcione como barreira de qualidade no ambiente isolado criado em J-01.

## Inventário técnico encontrado

| Área | Local | Situação inicial | Acção tomada |
| --- | --- | --- | --- |
| RegistrationTest | `tests/Feature/Auth/RegistrationTest.php` | Teste não enviava campos obrigatórios (`company_name`, `phone`) | Adicionados campos obrigatórios no teste |
| RegistrationTest | `tests/TestCase.php` | Role `subscriber` não existia no ambiente de teste | Criada role no `setUp()` do TestCase |
| RegistrationTest | `app/Http/Controllers/Auth/RegisteredUserController.php` | Job `ProvisionTenantMailDomain` podia falhar e bloquear o registo | Adicionado try-catch para o job |
| RegistrationTest | `app/Http/Controllers/Auth/RegisteredUserController.php` | Atribuição da role podia falhar e bloquear o registo | Adicionado try-catch para a role |
| ProfileTest | `resources/views/profile/partials/update-company-information-form.blade.php` | View tentava acessar `$user->company->name` quando empresa era `null` | Adicionada verificação `@if($user->company)` com fallback |
| ProfileTest | `resources/views/profile/edit.blade.php` | Partial de empresa incluída mesmo sem empresa | Adicionada verificação `@if(Auth::user()->company)` |
| ProfileTest | `app/Models/Companies.php` | Model não tinha `HasFactory` para testes | Adicionada trait `HasFactory` com namespace correcto |
| ProfileTest | `database/factories/CompaniesFactory.php` | Não existia factory para testes | Criada `CompaniesFactory` |
| ProfileTest | `app/Http/Controllers/ProfileController.php` | Validação de senha para exclusão de conta não funcionava | Adicionada validação `current_password` no método `destroy` |
| Acesso Admin | `database/seeders/InitialSeeder.php` | Seeder não criava empresa para o admin | Adicionada criação de empresa e `company_id` para admin e user |

## Fluxo actual

```text
Registo:
  Utilizador submete formulário com campos obrigatórios
  -> Empresa é criada
  -> Job ProvisionTenantMailDomain é disparado (não bloqueia)
  -> Usuário é criado com company_id
  -> Role 'subscriber' é atribuída (não bloqueia)
  -> Usuário é autenticado automaticamente
  -> Redirecionado para dashboard ou checkout

Perfil:
  Usuário acessa /profile
  -> Se tiver empresa: exibe formulário de edição
  -> Se não tiver empresa: exibe mensagem amigável

Exclusão de Conta:
  Usuário solicita exclusão
  -> Validação de senha (current_password)
  -> Se correta: logout e exclusão
  -> Se incorreta: erro retornado