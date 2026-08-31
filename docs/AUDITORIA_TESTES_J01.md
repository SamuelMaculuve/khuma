# J-01 — Isolamento da base de dados para a suite de testes

## Objectivo

Configurar para usar uma base de dados SQLite isolada, garantir que migrations e seeders funcionam nesse ambiente, e documentar como executar os testes com segurança — sem risco de tocar na base de desenvolvimento (`database/database.sqlite`).

## Inventário técnico encontrado

| Área | Local | Situação inicial | Acção tomada |
| --- | --- | --- | --- |
| Configuração de testes | `phpunit.xml` | Sem `DB_CONNECTION`/`DB_DATABASE` explícitos para o ambiente de testes | Definidas envs `DB_CONNECTION=sqlite` e `DB_DATABASE=:memory:` no bloco `<php>` |
| Base de testes | `tests/TestCase.php` | `use RefreshDatabase;` sem o import do namespace correcto | Adicionado `use Illuminate\Foundation\Testing\RefreshDatabase;` no topo do ficheiro |


## Fluxo actual

```text
php artisan test
  -> TestCase (RefreshDatabase)
  -> conexão sqlite :memory:
  -> migrations executadas do zero
  -> teste corre isolado
  -> base descartada ao fim do processo
```

## Mudanças já efectuadas

- `phpunit.xml` aponta para SQLite em memória, nunca para `database/database.sqlite`.
- Migrations confirmadas a correr no ambiente isolado: **23 de 25 testes** (59 assertions) passam, incluindo testes que dependem inteiramente de tabelas migradas (autenticação, verificação de email, reset de password, exclusão de conta).


## Evidência de validação local

- `php artisan test --filter=ExampleTest` — 2 testes passaram (0.02s e 5.13s), confirmando ambiente de teste funcional após a correcção do `TestCase.php`.
- `php artisan test` (suite completa) — 23 passaram, 2 falharam por motivo de aplicação já isolado; nenhum erro de conexão, driver ou tabela em falta.
- Nenhuma alteração foi feita à base de desenvolvimento (`database/database.sqlite`); a suite não a referencia em nenhum momento dos logs analisados.

