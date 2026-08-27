# R-01 — Auditoria da integração WhatsApp e segredos

> Estado inicial: 27 de agosto de 2026. Este documento não contém tokens, URLs privadas, números de telefone ou dados de clientes.

## Objectivo

Remover credenciais do código, identificar o percurso das chamadas WhatsApp e preparar a rotação segura das credenciais no provedor externo.

## Inventário técnico encontrado

| Área | Local | Situação inicial | Acção tomada no código |
| --- | --- | --- | --- |
| Criação de instância | `InstanceController::store` | Token administrativo Uazapi estava embutido | Token movido para `UAZAPI_ADMIN_TOKEN` |
| Pedido de conexão | `InstanceController::connect` | Token e telefone fixos no código | Token por ambiente; telefone obrigatório no request |
| Componente Livewire | `ConnectInstance` | Token e telefone preenchidos por valores fixos | Valores começam vazios e são validados |
| Endpoints Uazapi | Controllers/Livewire | URL repetida no código | URL e timeout centralizados em `config/services.php` |
| Configuração local | `.env.example` | Não havia variável para o token administrativo | Adicionadas `UAZAPI_ADMIN_TOKEN` e `UAZAPI_REQUEST_TIMEOUT` vazias |

## Fluxo actual

```text
Utilizador autenticado
  -> controller/componente Livewire
  -> Uazapi: connect, init, disconnect ou status
  -> resposta da API
  -> registo local de Instance / estado apresentado na interface
```

O fluxo de criação verifica o limite `whatsapp_instances` do plano. A fronteira por empresa e as permissões do CRM não substituem a auditoria das rotas e webhooks WhatsApp; essa revisão continua parte da R-03.

## Mudanças já efectuadas

- Não existe token administrativo literal nos dois fluxos de instância analisados.
- Erros HTTP são registados com estado e utilizador, sem colocar corpo de resposta ou token no log.
- Falta de configuração resulta numa mensagem segura e impede a chamada externa.
- O componente já não pré-preenche um telefone nem um token.

## Rotação externa obrigatória

Remover um segredo do código não invalida uma credencial que já foi exposta. O responsável com acesso ao provedor deve executar:

1. Revogar/rodar o token administrativo Uazapi exposto anteriormente.
2. Verificar se existem tokens de instância emitidos com a credencial anterior e revogá-los/rodá-los conforme a documentação do provedor.
3. Guardar o novo token somente no secret manager ou na variável `UAZAPI_ADMIN_TOKEN` do ambiente correcto.
4. Limpar caches de configuração apenas no ambiente alterado e testar uma chamada controlada, sem destinatários reais.
5. Registar data, pessoa responsável, ambiente e resultado da rotação num local de acesso restrito; não incluir o valor do token.

Não é possível realizar estes passos a partir deste repositório sem acesso autorizado à conta externa. Até a rotação estar confirmada, não activar envios ou criação de instâncias em produção.

## Pontos ainda pendentes

- Confirmar com o provedor o endpoint de status que actualmente recebe token no caminho da URL; avaliar alternativa que o transporte apenas em cabeçalho.
- Rever autenticação, assinatura e rate limit de todos os webhooks/endpoints em `routes/api.php` (R-03).
- Definir arquitectura oficial Meta/WhatsApp, opt-in, templates, custos e limites (R-02 e R-04).
- Proteger/rodar tokens de instância armazenados localmente e definir a política de encriptação/retenção.
- Executar teste de integração apenas após configurar credencial de desenvolvimento nova e descartável.

## Evidência de validação local

- Sintaxe PHP válida em `InstanceController.php`, `ConnectInstance.php` e `config/services.php`.
- Verificação de diff sem erros de espaços.
- Nenhum teste de integração foi disparado, para evitar contacto acidental com provedor externo e porque a suite ainda aguarda isolamento de base na J-01.
