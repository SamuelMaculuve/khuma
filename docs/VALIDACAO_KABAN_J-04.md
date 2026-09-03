# J-04 — Consistência e validação do Kanban

## Objectivo

Garantir que o estado apresentado no Kanban corresponde ao estado persistido na base de dados e que todas as transições de estado são válidas.

O foco desta implementação foi o `KanbanBoard`, o modelo `Leads`, as views Livewire do CRM e os respectivos testes, sem alterar migrations, sem introduzir estados configuráveis e sem modificar o design do Kanban.

A implementação também preserva as verificações de tenancy introduzidas no J-03.

## Inventário técnico

| Área | Ficheiro | Situação | Acção |
| --- | --- | --- | --- |
| Fonte de verdade dos estados | `app/Models/Leads.php` | Modificado | Centralizada a lista suportada de estados em `Leads::SUPPORTED_STATUSES`. |
| Kanban CRM | `app/Livewire/KanbanBoard.php` | Modificado | Validação de estados, movimentação após persistência, reconstrução a partir do estado persistido, pesquisa/filtros reais e manutenção das verificações de tenancy. |
| View do Kanban | `resources/views/livewire/kanban-board.blade.php` | Modificado | Filtros passaram a utilizar apenas estados suportados e atributos reais do modelo. |
| Testes de consistência | `tests/Feature/KanbanConsistencyTest.php` | Novo/Modificado | Criados testes para fonte de verdade, estados inválidos, movimentação, falha de persistência e pesquisa/filtros. |
| Testes de tenancy | `tests/Feature/CrmTenantIsolationTest.php` | Existente / validado | Suite do J-03 continua a passar após as alterações do J-04. |

## Ficheiros adicionados

```text
tests/Feature/KanbanConsistencyTest.php
```

## Ficheiros modificados

```text
app/Models/Leads.php
app/Livewire/KanbanBoard.php
resources/views/livewire/kanban-board.blade.php
```

## Testes implementados

A suite `KanbanConsistencyTest` verifica os seguintes cenários:

| Cenário | Resultado esperado |
| --- | --- |
| Estados suportados | `Leads::SUPPORTED_STATUSES` é a fonte de verdade |
| Criação com estado inválido | Operação rejeitada |
| Movimento para estado inválido | Operação rejeitada e estado persistido inalterado |
| Movimento válido | Base de dados actualizada e board reconstruído a partir do estado persistido |
| Falha de persistência | Cartão não fica movido apenas no estado do componente |
| Pesquisa e filtro | Utilizam atributos reais da Lead |

A suite `CrmTenantIsolationTest` continua a verificar os cenários de isolamento do J-03.


## Critérios de aceite

- [x] Não é possível gravar um estado fora da lista suportada.
- [x] Os estados suportados são centralizados numa única fonte de verdade.
- [x] Uma falha de persistência não move o cartão apenas no browser.
- [x] O board é reconstruído a partir do estado persistido após uma escrita bem-sucedida.
- [x] Pesquisa e filtros usam apenas atributos reais do modelo.
- [x] Pesquisa e filtros têm cobertura de testes.
- [x] Transições para estados inválidos são rejeitadas.
- [x] Fluxo de arrastar/mover continua funcional para dados da mesma empresa.
- [x] As verificações de tenancy do J-03 foram mantidas.
- [x] Não foram alteradas migrations.
- [x] Suite específica J-04/J-03: **11/11 testes passaram, 42 assertions**.
- [x] Suite completa: **40/40 testes passaram, 116 assertions**.

## Estado final

**J-04 — CONCLUÍDO**

O Kanban passa a tratar o estado persistido da Lead como referência efectiva, rejeitando estados não suportados e só reflectindo uma movimentação na interface depois de a escrita ser confirmada.

A pesquisa e os filtros foram alinhados com os atributos reais do modelo, enquanto as protecções de tenancy do J-03 permanecem activas.

**Validação final: 40 testes passaram, 116 assertions, sem falhas.**
