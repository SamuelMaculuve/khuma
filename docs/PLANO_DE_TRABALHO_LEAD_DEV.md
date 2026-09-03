# Plano de trabalho — `Lead_dev`

> Estado analisado em 27 de agosto de 2026. Este plano parte do código actual da branch `Lead_dev`, do quadro de tarefas e da execução local validada. A ordem privilegia segurança, isolamento entre empresas, conformidade e confiabilidade antes de expandir funcionalidades comerciais.

## Objectivo

Transformar a `Lead_dev` numa base segura e previsível para o produto Khuma: CRM multiempresa, WhatsApp, campanhas de email, planos/subscrições e integrações de email.

## Regras de trabalho

- A branch de integração é `Lead_dev`. Cada tarefa deve ser feita numa branch curta a partir dela e chegar por pull request.
- Não colocar tokens, palavras-passe, chaves de Mailcow, Cloudflare, Uazapi/WhatsApp ou M-Pesa em código, commits, capturas de ecrã ou issues.
- Toda alteração que lê ou escreve dados de empresa deve validar a `company_id` do utilizador autenticado no servidor. Validar apenas `exists:id` não é suficiente.
- Não activar envios reais, criação de domínios, DNS ou cobrança enquanto a auditoria não estiver concluída e aprovada.
- Cada PR inclui: descrição, risco, testes executados, evidência visual quando altera UI e passos de rollback quando altera dados/integrações.

## Ordem de prioridade

| Prioridade | Frente | Dono | Dependência / motivo |
| --- | --- | --- | --- |
| P0 | Revogar segredos e auditar a integração WhatsApp actual | Responsável técnico | Há tokens embutidos no código e ainda não há inventário confiável do fluxo, custos e dados. |
| P0 | Tornar instalação e testes repetíveis | Jair | Permite validar todas as entregas sem contaminar a base local. |
| P0 | Corrigir regressões de autenticação/perfil | Jair | A suite actual tem duas falhas; a base precisa voltar a passar antes de evoluir. |
| P0 | Isolamento de dados no CRM/Kanban | Jair, com revisão | É risco directo de uma empresa ver ou alterar dados de outra. |
| P1 | Corrigir consistência do Kanban | Jair | Depende do isolamento; evita estados inválidos e UI divergente da base de dados. |
| P1 | Definir política, custos e arquitectura Meta | Responsável técnico | Determina se/quando migrar para Cloud API e quais limites vender. |
| P1 | Modelo comercial e limites de planos | Responsável técnico | Deve nascer da política aprovada e dos custos reais, não de suposições. |
| P1 | Garantias de envio de campanhas | Jair, com revisão | Evita reenvios/duplicados; só activar envios após as tarefas P0. |
| P2 | Integração operacional de email/Mailcow/DNS | Responsável técnico | Usa credenciais, DNS e recursos externos; depende de limites e entrega confiável. |
| P2 | Templates e acabamento de campanhas | Jair | Só faz sentido quando consentimento, limite e entrega estiverem definidos. |
| P3 | Lançamento controlado e observabilidade | Responsável técnico | Fecha a preparação para clientes reais. |

## Sequência recomendada

1. **Em paralelo:** eu executo R-01 e Jair executa J-01.
2. Jair conclui J-02 e J-03; eu reviso cada PR antes de merge.
3. Jair avança para J-04. Em paralelo, eu concluo R-02 e R-03, que produzem as decisões necessárias para planos e WhatsApp.
4. Depois da definição comercial, eu executo R-04 e aprovo o desenho de email de R-05.
5. Jair implementa J-05; apenas então iniciamos J-06 e a configuração externa controlada de R-05.

---

## Tarefas do Jair

Estas tarefas foram escolhidas por serem verificáveis, reversíveis e terem fronteiras claras. Jair não deve usar credenciais de produção, mexer em DNS/Mailcow/Cloudflare/Meta/M-Pesa, nem alterar regras comerciais sem revisão.

### J-01 — Base de desenvolvimento e testes isolados

**Prioridade:** P0
**Porque:** o repositório instala e compila, mas o `.env.example` actual começa com uma linha que não é uma variável de ambiente. Além disso, `phpunit.xml` não isola explicitamente a base de testes. Isso torna o primeiro arranque confuso e pode fazer testes escreverem na base de desenvolvimento.

**Resultado esperado:** uma cópia nova do repositório pode preparar o ambiente local sem edição improvisada, e os testes usam uma base exclusiva de teste.

**Escopo:**

- Remover a linha inválida `php artisan db:seed --class=EmployeeSeeder` do início de `.env.example`.
- Configurar `phpunit.xml` e/ou a configuração de testes para SQLite em memória ou `database/testing.sqlite`, sem reutilizar `database/database.sqlite` de desenvolvimento.
- Garantir que migrations e seeders necessários à suite funcionam nesse ambiente isolado.
- Documentar, num comentário conciso ou no onboarding versionado, como executar os testes com segurança.

**Ficheiros de referência:** `.env.example`, `phpunit.xml`, `tests/`, `database/migrations/`, `database/seeders/`.

**Fora do escopo:** alterar funcionalidades do produto, apagar a base local, alterar credenciais ou instalar serviços externos.

**Critérios de aceite:**

- `php artisan test` não altera a base de desenvolvimento.
- `php artisan test` executa numa cópia nova depois de `composer install` e da preparação documentada.
- `npm run build` continua a terminar com sucesso.
- A alteração contém um teste ou verificação que demonstra o isolamento.

### J-02 — Corrigir as regressões actuais de autenticação e perfil

**Prioridade:** P0
**Porque:** a suite actual falha em dois cenários básicos: o registo não autentica o utilizador como o teste espera e o perfil pode dar erro quando o utilizador não tem empresa associada. Estes erros tornam a aplicação inconsistente e impedem que a suite seja uma barreira de qualidade.

**Resultado esperado:** todos os testes de `tests/Feature/Auth` e `tests/Feature/ProfileTest.php` passam de forma isolada.

**Escopo:**

- Reproduzir as falhas em `RegistrationTest` e `ProfileTest` no ambiente criado em J-01.
- Decidir e implementar o comportamento coerente de registo: se o produto deve iniciar sessão após criar conta, corrigir o fluxo e o teste; se não deve, corrigir o requisito/teste com revisão explícita.
- Tornar a página de perfil segura para utilizadores sem empresa: criar/garantir a associação no fluxo apropriado ou renderizar um estado válido sem aceder a uma relação nula.
- Adicionar cenários de teste para utilizador com empresa e sem empresa.

**Ficheiros de referência:** `app/Http/Controllers/Auth/RegisteredUserController.php`, views/componentes de perfil, `tests/Feature/Auth/RegistrationTest.php`, `tests/Feature/ProfileTest.php`.

**Fora do escopo:** redesenhar onboarding comercial, mudar roles ou criar empresas reais por integração externa.

**Critérios de aceite:**

- `php artisan test --filter=RegistrationTest` passa.
- `php artisan test --filter=ProfileTest` passa.
- A suite completa passa no ambiente de testes isolado.
- O PR explica qual comportamento de negócio foi mantido no registo.

### J-03 — Fechar acessos cruzados entre empresas no CRM

**Prioridade:** P0
**Porque:** o Kanban e algumas rotas validam IDs globais de cliente/equipa, mas não asseguram que pertencem à empresa do utilizador. Em um SaaS, isso pode permitir associar, visualizar ou manipular dados de outro cliente/empresa.

**Resultado esperado:** toda leitura e escrita de leads, clientes e equipas no CRM é limitada à empresa autenticada.

**Escopo:**

- Criar ou aplicar policies/queries de tenancy para `Leads`, `Client` e `Team` conforme o padrão já usado no projecto.
- No `KanbanBoard`, aceitar cliente/equipa apenas quando pertencem à `company_id` actual.
- Garantir que o detalhe da lead e rotas de recursos não aceitam uma lead de outra empresa por URL.
- Cobrir com testes de duas empresas: utilizador A não lista, não vê, não actualiza e não associa recursos de B.

**Ficheiros de referência:** `app/Livewire/KanbanBoard.php`, `app/Http/Controllers/LeadsController.php`, `routes/web.php`, modelos e migrations de leads/clientes/equipas, `app/Policies/` se existir.

**Fora do escopo:** criar uma nova hierarquia de permissões, mexer nos limites dos planos ou alterar o design do Kanban.

**Critérios de aceite:**

- Requests directos a URL/Livewire com ID de outra empresa recebem 403/404 ou ignoram a acção sem vazar dados.
- Testes cobrem lista, detalhe, criação/edição e associação de lead a cliente/equipa.
- Não há consulta que use apenas um ID recebido do browser sem filtro por `company_id` nesse fluxo.

### J-04 — Consistência e validação do Kanban

**Prioridade:** P1, depois de J-03
**Porque:** a interface permite estados dinâmicos que podem divergir do enum persistido; a movimentação pode alterar a UI mesmo quando a escrita não foi efectuada; e os filtros referem campos que não correspondem ao modelo actual. Isso gera uma percepção enganadora ao utilizador.

**Resultado esperado:** o estado mostrado no Kanban é o estado persistido e todas as transições são válidas.

**Escopo:**

- Definir uma única fonte de verdade para os estados (`new`, `contacted`, `qualified`, `proposal`, `negotiation`, `won`, `lost`) ou criar a migration/modelo necessário antes de aceitar estados configuráveis.
- Validar estados no servidor e devolver erro claro para transições inválidas.
- Actualizar o estado visual somente após confirmação de escrita bem-sucedida.
- Corrigir ou remover filtros/campos inexistentes (`requester`, `service`, `priority`) e testar pesquisa/filtro real.
- Manter todas as verificações de tenancy de J-03.

**Ficheiros de referência:** `app/Livewire/KanbanBoard.php`, modelo/migration `Leads`, views Livewire do CRM e respectivos testes.

**Fora do escopo:** novo design, automações de vendas, importação de leads ou integração WhatsApp.

**Critérios de aceite:**

- Não é possível gravar um estado fora da lista suportada.
- Uma falha de persistência não move o cartão apenas no browser.
- Pesquisa e filtros usam apenas atributos reais e têm testes.
- Fluxo de arrastar/mover continua funcional para dados da mesma empresa.

### J-05 — Entrega idempotente de campanhas de email

**Prioridade:** P1, depois de R-04 e com revisão
**Porque:** o job agendado pode reenviar uma campanha ao mesmo destinatário quando há repetição, concorrência ou novo processamento. Envio duplicado prejudica reputação, custos e confiança.

**Resultado esperado:** para uma campanha/destinatário, a aplicação envia no máximo uma vez, salvo uma acção explícita e auditável de reenvio.

**Escopo:**

- Mapear os estados de campanha e o registo de cada envio existentes.
- Introduzir uma garantia de idempotência no banco (por exemplo, chave única campanha+destinatário) e/ou lock atómico adequado ao driver usado.
- Fazer o job seleccionar apenas destinatários ainda elegíveis, marcar estados de forma transaccional e lidar com falha/retry sem duplicar.
- Criar testes para: job repetido, dois jobs concorrentes simulados, falha antes/depois do envio e agendamento executado mais de uma vez.
- Registar resultados mínimos de entrega/falha sem dados sensíveis em logs.

**Ficheiros de referência:** `app/Jobs/SendCampaignEmails.php`, `app/Console/Commands/DispatchScheduledEmailCampaigns.php`, modelos/migrations de campanhas e logs, `routes/console.php`.

**Fora do escopo:** configurar Mailcow, DNS, IMAP, conteúdo comercial, preços e disparar emails reais.

**Critérios de aceite:**

- Reexecutar o dispatcher/job não cria segundo envio para o mesmo par campanha/destinatário.
- Falhas permanecem recuperáveis e observáveis.
- A suite usa `Mail::fake()`/fakes equivalentes; nenhum teste envia email externo.
- O responsável técnico aprova a estratégia antes do merge.

### J-06 — Qualidade da biblioteca de templates de email

**Prioridade:** P2, depois de J-05 e R-05
**Porque:** templates só geram valor quando o envio, limites, consentimento e domínio remetente já são confiáveis. É uma boa frente de interface e dados para consolidar depois da base operacional.

**Resultado esperado:** utilizadores autorizados conseguem escolher, pré-visualizar e usar templates consistentes nas campanhas.

**Escopo:**

- Rever os seeders e campos dos templates existentes.
- Corrigir validação, placeholders permitidos e escape seguro da pré-visualização.
- Criar testes de criação/edição/uso e estados vazios da interface.
- Implementar apenas os limites e regras de acesso definidos em R-04.

**Ficheiros de referência:** `app/Livewire/EmailCampaigns.php`, modelos/seeders de template, views de email marketing e testes Livewire.

**Fora do escopo:** criar campanhas reais, alterar a política de opt-in ou alterar configurações de SMTP.

**Critérios de aceite:**

- Placeholders inválidos são rejeitados ou mostrados de forma segura.
- A pré-visualização não executa HTML/script não confiável.
- Um utilizador não vê templates de outra empresa, se os templates forem tenant-specific.

---

## Tarefas do responsável técnico

Estas tarefas ficam comigo porque envolvem incident response, escolha de fornecedor/arquitectura, dinheiro, políticas externas, produção ou credenciais. Jair pode ajudar com investigação ou testes apenas após uma divisão específica.

### R-01 — Resposta a segredos expostos e auditoria da integração actual

**Prioridade:** P0
**Porque:** há tokens hardcoded nos fluxos de WhatsApp/Uazapi. Um token exposto deve ser tratado como comprometido, mesmo se o repositório for privado.

**Entregáveis:**

- Inventário de endpoints, webhooks, tokens, contas externas, origem/destino de mensagens e dados pessoais.
- Revogação/rotação dos segredos expostos no provedor e remoção do histórico activo quando aplicável, seguindo procedimento seguro.
- Configuração por variáveis de ambiente/secret store, validação de configuração ausente e nenhuma credencial em logs.
- Decisão formal de congelar ou limitar envios até haver conformidade.

**Locais a rever:** `app/Http/Controllers/InstanceController.php`, `app/Livewire/ConnectInstance.php`, `.env.example`, `config/services.php`, `routes/api.php`, webhooks e configuração do provedor.

**Aceite:** não restam credenciais funcionais hardcoded; há lista de fluxos e donos; os webhooks são autenticados, auditáveis e não expõem dados de outra empresa.

### R-02 — Política Meta/WhatsApp, custos e decisão de arquitectura

**Prioridade:** P1
**Porque:** o quadro pede adaptação à política Meta, opt-in, rate limiting e templates aprovados; o código actual usa Uazapi/QR, não uma integração oficial Meta Cloud API. A decisão afecta conformidade, produto e margem.

**Entregáveis:**

- Documento de decisão: manter temporariamente o conector actual com limites, migrar para WhatsApp Business Platform/Cloud API ou retirar a oferta até a conformidade estar disponível.
- Matriz de requisitos: opt-in verificável, finalidade da mensagem, opt-out, templates, janelas/rate limits, retenção de dados, auditoria e incidentes.
- Modelo de custos versionado por mercado e tipo de mensagem, com data de verificação e margem alvo. Preços/regras devem ser confirmados nas fontes oficiais antes de publicar.
- Roadmap técnico de migração: onboarding de WABA, webhooks verificados, filas, assinatura de eventos, templates e monitorização.

**Aceite:** decisão aprovada por produto/negócio; não há tabela de preços baseada em valores não verificados; requisitos transformados em critérios de aceite técnicos.

### R-03 — Reforçar a fronteira administrativa e APIs

**Prioridade:** P1
**Porque:** as definições de empresa/email/equipa são sensíveis; algumas APIs e acções precisam de autorização e tenancy consistentes. A administração de utilizadores actualmente inclui uma password fixa, que não é aceitável para operação real.

**Entregáveis:**

- Rever `Settings`, equipas, gestão de utilizadores, rotas API e logs de chamadas.
- Exigir papel apropriado (por exemplo, administrador da empresa) e validar membros da equipa dentro da empresa.
- Substituir criação com password fixa por convite, reset seguro ou fluxo definido pelo produto.
- Aplicar autenticação, rate limit, validação de assinatura/segredo onde aplicável e testes de autorização às APIs.

**Aceite:** utilizador comum não altera configuração crítica; APIs não expõem/aceitam dados sem autenticação/autorização; nenhum fluxo gera password previsível.

### R-04 — Planos, subscrições e limites que sustentam o produto

**Prioridade:** P1, depois de R-02
**Porque:** planos existem, mas os limites de email/WhatsApp/equipas precisam representar custos e políticas reais. Cobrança ou bloqueio sem definição inequívoca cria risco financeiro e suporte manual.

**Entregáveis:**

- Catálogo de capacidades por plano: utilizadores, equipas, clientes, campanhas, destinatários, domínios/remetentes, mensagens WhatsApp, templates e excedentes.
- Definição de contabilização, reset de ciclo, upgrade/downgrade, período de carência e comportamento ao atingir limite.
- Revisão do fluxo de M-Pesa, confirmando o que é simulação e o que é produção; webhooks idempotentes e conciliação.
- Feature gates no servidor, mensagens de UI e testes de limite.

**Aceite:** cada limite tem unidade, fonte de consumo e teste; o cliente não consegue contornar via HTTP/queue; pagamentos são rastreáveis e idempotentes.

### R-05 — Operação de email por empresa (Mailcow, DNS e recepção)

**Prioridade:** P2, depois de R-04 e J-05
**Porque:** o projecto já contém provisionamento Mailcow/Cloudflare, SMTP e IMAP, mas isto toca DNS, contas, reputação de domínio e credenciais. A extensão IMAP não está disponível no ambiente local actual.

**Entregáveis:**

- Desenho de lifecycle do domínio/remetente por empresa: criação, validação DNS, SPF/DKIM/DMARC, falha, rotação e remoção.
- Separação de credenciais por ambiente e por empresa, com rotação e menor privilégio.
- Decisão operacional para IMAP: instalar/extensão compatível ou adoptar webhook/provedor; fila e scheduler monitorizados.
- Sandbox de testes sem DNS/email real e checklist de activação por tenant.

**Aceite:** não se provisiona domínio real por acidente; TLS permanece validado; há observabilidade para fila, envio e inbound; cada operação externa tem rollback ou procedimento de compensação.

### R-06 — Revisão de lançamento controlado

**Prioridade:** P3
**Porque:** a aplicação só deve receber clientes reais quando segurança, limites, integrações e monitorização formarem um conjunto operacional.

**Entregáveis:** checklist de release, ambiente staging, backup/rollback, logs/alertas, testes de ponta a ponta, piloto com poucos tenants e processo de suporte.

**Aceite:** piloto concluído sem vazamento/duplicação/cobrança incorrecta e decisão de go/no-go registada.

---

## Definição de pronto para qualquer tarefa

- Código formatado e sem segredos.
- Teste automatizado para a regra alterada; para interface, evidência visual adicional.
- `php artisan test` e `npm run build` executados, ou falha conhecida descrita no PR.
- Migrations são aditivas, reversíveis quando possível e acompanhadas de plano para dados existentes.
- Mudanças de autorização incluem um teste de acesso negado.
- Alterações de jobs/webhooks incluem teste de repetição/idempotência.
- O PR não mistura refactor amplo com alteração de comportamento sem necessidade.

## Primeira distribuição prática

**Jair inicia J-01.** Assim terá uma entrega curta, compreenderá a estrutura Laravel e deixará uma base segura para os próximos testes.
**Eu inicio R-01.** Nenhuma tarefa de WhatsApp/email com credenciais ou envio externo deve avançar antes da auditoria e rotação dos segredos.
