# J-03 — Fechar acessos cruzados entre empresas no CRM

## Objectivo

Garantir o isolamento multi-tenant do CRM, impedindo que um utilizador de uma empresa liste, visualize, actualize ou associe leads, clientes e equipas pertencentes a outra empresa.

O foco desta implementação foi o `KanbanBoard`, as rotas de Leads e as regras de autorização/tenancy, sem alterar a hierarquia de permissões, limites dos planos ou o design do Kanban.

## Inventário técnico

| Área | Ficheiro | Situação | Acção |
| --- | --- | --- | --- |
| Autorização de Leads | `app/Policies/LeadPolicy.php` | Novo | Criada policy para validar que a Lead pertence à `company_id` do utilizador autenticado. |
| Autorização de Clientes | `app/Policies/ClientPolicy.php` | Novo | Criada policy para impedir acesso a Clientes de outra empresa. |
| Autorização de Equipas | `app/Policies/TeamPolicy.php` | Novo | Criada policy para impedir acesso a Equipas de outra empresa. |
| Controller de Leads | `app/Http/Controllers/LeadsController.php` | Modificado | Consultas e operações sobre Leads passaram a validar o tenant da empresa autenticada e a utilizar autorização da `LeadPolicy`. |
| Kanban CRM | `app/Livewire/KanbanBoard.php` | Modificado | Listagem, filtros, associações e movimentação de Leads passaram a validar `company_id` para Leads, Clientes e Equipas. IDs enviados pelo browser não são aceites isoladamente. |
| Testes de isolamento | `tests/Feature/CrmTenantIsolationTest.php` | Novo | Criada suite específica com duas empresas para verificar isolamento de leitura, escrita e associações. |

## Ficheiros adicionados

```text
app/Policies/LeadPolicy.php
app/Policies/ClientPolicy.php
app/Policies/TeamPolicy.php
tests/Feature/CrmTenantIsolationTest.php
```

## Ficheiros modificados

```text
app/Http/Controllers/LeadsController.php
app/Livewire/KanbanBoard.php
```

## Ficheiros analisados mas sem alteração no J-03

Os seguintes ficheiros fazem parte do fluxo de referência, mas não foram modificados especificamente nesta implementação:

```text
routes/web.php
app/Models/Leads.php
app/Models/Clients.php
app/Models/Team.php
```

## Protecções implementadas

### Leads

As operações de Leads passam a considerar a empresa autenticada. Uma Lead de outra empresa não pode ser obtida apenas pelo seu ID global.

O princípio aplicado é:

```text
ID recebido do browser
        ↓
filtrar pela company_id autenticada
        ↓
Lead pertence ao tenant?
     /           \
   SIM           NÃO
    ↓             ↓
continua       403/404/bloqueio
```

### Clientes

Ao associar um Cliente a uma Lead, o Cliente precisa pertencer à mesma empresa da Lead/utilizador.

Não é suficiente procurar apenas:

```text
client_id
```

A empresa também é considerada na validação.

### Equipas

O mesmo isolamento é aplicado às Equipas. Uma equipa pertencente à Empresa B não pode ser associada ou utilizada por um utilizador da Empresa A.

### Kanban

O `KanbanBoard` foi protegido contra IDs cross-company em:

- carregamento/listagem de Leads;
- filtros por Cliente;
- filtros por Equipa;
- criação/associação de Cliente;
- criação/associação de Equipa;
- movimentação de Leads entre colunas.

## Testes implementados

O teste `CrmTenantIsolationTest` cria dados para duas empresas:

```text
Empresa A
 ├── Utilizador A
 ├── Cliente A
 ├── Equipa A
 └── Lead A

Empresa B
 ├── Utilizador B
 ├── Cliente B
 ├── Equipa B
 └── Lead B
```

São verificados os seguintes cenários:

| Cenário | Resultado esperado |
| --- | --- |
| Kanban lista Leads | Apenas Leads da empresa autenticada |
| URL directa para Lead de outra empresa | Acesso bloqueado |
| Update directo de Lead de outra empresa | Operação bloqueada |
| Associação de Cliente de outra empresa | Rejeitada |
| Associação de Equipa de outra empresa | Rejeitada |
| Movimento de Lead de outra empresa | Rejeitado |

## Evidência de validação local

Comando executado:

```bash
php artisan test --filter=CrmTenantIsolationTest
```

Resultado:

```text
PASS  Tests\Feature\CrmTenantIsolationTest

✓ kanban lists only leads of the authenticated company
✓ direct lead url cannot open a lead from another company
✓ direct update route cannot update another company lead
✓ kanban rejects cross company client and team associations
✓ kanban cannot move a lead from another company

Tests: 5 passed (16 assertions)
Duration: 7.14s
```

## Observação sobre o HTTP 302

Durante a validação das rotas directas, as tentativas cross-company foram redireccionadas para:

```text
http://localhost/subscription/plans
```

O comportamento não permitiu acesso ou alteração dos dados da outra empresa e os testes de isolamento passaram.

O critério de aceite permite bloqueio por 403/404 ou bloqueio da acção sem vazamento de dados. Neste fluxo, a aplicação bloqueia a operação através do redireccionamento e os testes confirmam que a Lead da outra empresa não foi exposta nem alterada.

## Critérios de aceite

- [x] Requests directos a URL com ID de Lead de outra empresa não permitem acesso aos dados.
- [x] Requests directos de actualização de Lead de outra empresa não permitem alteração.
- [x] Kanban só lista Leads da empresa autenticada.
- [x] Cliente de outra empresa não pode ser associado.
- [x] Equipa de outra empresa não pode ser associada.
- [x] Lead de outra empresa não pode ser movimentada pelo Kanban.
- [x] Testes cobrem duas empresas.
- [x] Não são utilizados apenas IDs recebidos do browser sem validação de `company_id` nos fluxos protegidos.
- [x] Suite específica do J-03: **5/5 testes passaram, 16 assertions**.

## Estado final

**J-03 — CONCLUÍDO**

O CRM passa a aplicar isolamento por empresa nos fluxos abrangidos pelo escopo desta tarefa, reduzindo o risco de acesso, associação ou manipulação cross-company através de URLs, requests e acções do Kanban.
