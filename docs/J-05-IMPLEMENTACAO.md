# J-05 — Entrega idempotente de campanhas de email

## Objectivo

Garantir que uma campanha de email não seja enviada mais de uma vez ao mesmo destinatário quando o job é repetido, quando existem execuções concorrentes ou quando o dispatcher agendado é executado mais de uma vez.

O foco desta implementação foi o `SendCampaignEmails`, o dispatcher `DispatchScheduledEmailCampaigns`, o registo existente em `EmailCampaignLog` e os respectivos testes, sem alterar migrations e sem disparar emails reais.

A estratégia adopta **at-most-once** para o par campanha/destinatário, mantendo falhas recuperáveis quando a aplicação sabe que o envio não chegou ao transporte e evitando retry automático quando o resultado do transporte é ambíguo.

## Inventário técnico

| Área | Ficheiro | Situação | Acção |
| --- | --- | --- | --- |
| Job de envio | `app/Jobs/SendCampaignEmails.php` | Modificado | Adicionada unicidade do job por campanha, lock partilhado por campanha, selecção por registo existente, estados `sent`/`pending` como barreira contra reenvio e tratamento diferenciado de falhas conhecidas/ambíguas. |
| Dispatcher agendado | `app/Jobs/DispatchScheduledEmailCampaigns.php` | Modificado | Adicionada unicidade para a janela de execução do dispatcher e despacho dos jobs de campanha apenas para campanhas agendadas e vencidas. |
| Registo de entrega | `app/Models/EmailCampaignLog.php` | Existente / utilizado | O registo existente `email_campaign_logs` passa a funcionar como claim persistente do par campanha/destinatário, sem alteração de schema. |
| Testes de idempotência | `tests/Feature/EmailCampaignIdempotencyTest.php` | Novo | Criados testes para repetição do job, concorrência, falhas, estado `pending`, repetição do dispatcher e ausência de email do destinatário nos logs. |
| Scheduler | `routes/console.php` | Existente / validado | Mantido o agendamento existente, sem alteração de migrations ou de configuração comercial. |


## Estratégia de idempotência

A protecção foi implementada em várias camadas:

| Mecanismo | Função |
| --- | --- |
| `ShouldBeUnique` no `SendCampaignEmails` | Evita que o mesmo job de campanha seja colocado/processado repetidamente durante a janela de unicidade. |
| Lock `email-campaign-send:{campaign_id}` | Impede dois workers de processarem simultaneamente a mesma campanha e disputarem o primeiro registo de cada destinatário. |
| `EmailCampaignLog` existente | Mantém o claim persistente para o par `email_campaign_id` + `client_id`. |
| Estado `sent` | Impede qualquer novo envio automático para o destinatário já enviado. |
| Estado `pending` | Representa uma tentativa cujo resultado de transporte é ambíguo e impede retry automático. |
| Estado `failed` | Permanece recuperável e pode ser processado novamente por uma execução explícita. |
| `message_id` determinístico | Mantém um identificador estável para o mesmo par campanha/destinatário. |
| `ShouldBeUnique` no dispatcher | Evita múltiplas execuções concorrentes do dispatcher dentro da janela definida. |



## Testes implementados

A suite `EmailCampaignIdempotencyTest` verifica os seguintes cenários:

| Cenário | Resultado esperado |
| --- | --- |
| Job repetido para a mesma campanha | Apenas um envio para cada par campanha/destinatário |
| Segundo worker com lock activo | Execução ignorada e nenhum segundo envio |
| Destinatário anteriormente `failed` | Pode ser recuperado numa execução posterior |
| Registo `pending` existente | Não é reenviado automaticamente |
| Dispatcher executado mais de uma vez | Não cria uma segunda entrega |
| Falha de transporte | Estado permanece `pending` e o log não contém o email |
| Excepção depois de possível aceitação pelo provider | Nova execução não chama o transporte novamente |

A suite utiliza `Mail::fake()` ou mocks equivalentes. Nenhum teste envia email para um serviço externo.

## Critérios de aceite

- [x] Reexecutar o job não cria segundo envio para o mesmo par campanha/destinatário.
- [x] Execuções concorrentes da mesma campanha são protegidas por lock atómico.
- [x] Reexecutar o dispatcher não cria segundo envio.
- [x] Estados `sent` e `pending` não são reenviados automaticamente.
- [x] Falhas conhecidas antes do transporte permanecem recuperáveis.
- [x] Falhas de transporte ambíguas permanecem observáveis e não são repetidas automaticamente.
- [x] O endereço de email do destinatário não é registado nos logs da implementação.
- [x] A suite utiliza fakes/mocks e não envia emails externos.
- [x] Foi introduzido `message_id` determinístico para o par campanha/destinatário.
- [x] Não foram alteradas migrations.
- [ ] Suite PHPUnit J-05 executada no ambiente completo.
- [ ] Revisão/aprovação da estratégia pelo responsável técnico antes do merge.


