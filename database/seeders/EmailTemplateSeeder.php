<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->templates() as $tpl) {
            EmailTemplate::updateOrCreate(
                ['slug' => $tpl['slug']],
                array_merge($tpl, ['is_global' => true, 'company_id' => null]),
            );
        }
    }

    private function templates(): array
    {
        return [
            [
                'slug'      => 'plain',
                'name'      => 'Texto simples',
                'category'  => 'basic',
                'subject'   => 'Mensagem de {{company_name}}',
                'thumbnail' => null,
                'placeholders' => [],
                'body_html' => <<<'HTML'
<p style="margin:0 0 18px;color:#273241;font-size:15px;line-height:1.65;">Olá <strong>{{name}}</strong>,</p>
<p style="margin:0 0 18px;color:#273241;font-size:15px;line-height:1.65;">Escreva aqui a sua mensagem principal. Mantenha o texto direto, claro e focado numa única ação ou informação importante.</p>
<p style="margin:0 0 24px;color:#273241;font-size:15px;line-height:1.65;">Pode adicionar links, imagens e detalhes adicionais, sempre com espaçamento simples para o email ficar fácil de ler em qualquer dispositivo.</p>
<p style="margin:0;color:#273241;font-size:15px;line-height:1.65;">Cumprimentos,<br><strong>{{company_name}}</strong></p>
HTML,
            ],
            [
                'slug'      => 'welcome',
                'name'      => 'Boas-vindas',
                'category'  => 'onboarding',
                'subject'   => 'Bem-vindo à {{company_name}}!',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem principal', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 28px;display:block;" />
<h1 style="margin:0 0 16px;color:#111827;font-size:28px;line-height:1.25;">Bem-vindo(a), {{name}}</h1>
<p style="margin:0 0 18px;color:#273241;font-size:15px;line-height:1.65;">É um prazer recebê-lo(a) na {{company_name}}. Preparámos os primeiros passos para que comece com clareza e tire valor desde o primeiro contacto.</p>
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">Nas próximas mensagens vamos partilhar orientações, novidades e recursos importantes para a sua experiência.</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:26px 0 30px;">
  <tr>
    <td bgcolor="#245f95" style="padding:13px 22px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Começar agora</a>
    </td>
  </tr>
</table>
<p style="margin:0;color:#64748b;font-size:13px;line-height:1.6;">Se tiver qualquer dúvida, responda a este email e a nossa equipa acompanha-o(a).</p>
HTML,
            ],
            [
                'slug'      => 'promo',
                'name'      => 'Promoção',
                'category'  => 'marketing',
                'subject'   => 'Oferta especial só para si, {{name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Banner da promoção', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<p style="margin:0 0 8px;color:#b45309;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Oferta por tempo limitado</p>
<h1 style="margin:0 0 16px;color:#111827;font-size:30px;line-height:1.2;">Condição especial para si, {{name}}</h1>
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 24px;display:block;" />
<p style="margin:0 0 18px;color:#273241;font-size:15px;line-height:1.65;">Preparámos uma oferta exclusiva da {{company_name}} para clientes selecionados. Use o código <strong>SAVE30</strong> e poupe 30% na sua próxima encomenda.</p>
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">A campanha termina no fim da semana e está sujeita à disponibilidade dos produtos ou serviços elegíveis.</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 6px;">
  <tr>
    <td bgcolor="#b91c1c" style="padding:14px 24px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Aproveitar oferta</a>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'newsletter',
                'name'      => 'Newsletter',
                'category'  => 'newsletter',
                'subject'   => 'Novidades da {{company_name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem de destaque', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<p style="margin:0 0 8px;color:#245f95;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Newsletter</p>
<h1 style="margin:0 0 16px;color:#111827;font-size:28px;line-height:1.25;">Novidades da {{company_name}}</h1>
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">Olá {{name}}, reunimos os principais destaques deste mês para que acompanhe as novidades, decisões e oportunidades mais relevantes.</p>
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<h2 style="margin:0 0 12px;color:#111827;font-size:21px;line-height:1.3;">Destaque do mês</h2>
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">Substitua este texto pelo resumo principal da sua newsletter. Comece pelo impacto para o cliente e termine com uma ação clara.</p>
<hr style="border:0;border-top:1px solid #e2e8f0;margin:26px 0;" />
<h3 style="margin:0 0 12px;color:#111827;font-size:17px;line-height:1.35;">Outras atualizações</h3>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td style="padding:0 0 14px;border-bottom:1px solid #e2e8f0;">
      <p style="margin:0;color:#111827;font-weight:700;font-size:14px;">Atualização 1</p>
      <p style="margin:4px 0 0;color:#475569;font-size:14px;line-height:1.6;">Descreva aqui a primeira notícia de forma breve.</p>
    </td>
  </tr>
  <tr>
    <td style="padding:14px 0;border-bottom:1px solid #e2e8f0;">
      <p style="margin:0;color:#111827;font-weight:700;font-size:14px;">Atualização 2</p>
      <p style="margin:4px 0 0;color:#475569;font-size:14px;line-height:1.6;">Descreva aqui a segunda notícia de forma breve.</p>
    </td>
  </tr>
  <tr>
    <td style="padding:14px 0 0;">
      <p style="margin:0;color:#111827;font-weight:700;font-size:14px;">Atualização 3</p>
      <p style="margin:4px 0 0;color:#475569;font-size:14px;line-height:1.6;">Descreva aqui a terceira notícia de forma breve.</p>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'reengagement',
                'name'      => 'Reactivação',
                'category'  => 'marketing',
                'subject'   => 'Sentimos a sua falta, {{name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<p style="margin:0 0 8px;color:#245f95;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Queremos voltar a falar consigo</p>
<h1 style="margin:0 0 16px;color:#111827;font-size:28px;line-height:1.25;">Olá {{name}}, temos novidades para partilhar</h1>
<p style="margin:0 0 18px;color:#273241;font-size:15px;line-height:1.65;">Lançámos melhorias e novas condições desde a última vez que esteve connosco. A {{company_name}} preparou uma atualização rápida para o(a) ajudar a retomar o contacto.</p>
<p style="margin:0 0 24px;color:#273241;font-size:15px;line-height:1.65;">Se ainda fizer sentido para si, veja o que mudou ou responda a este email para falar diretamente com a nossa equipa.</p>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 6px;">
  <tr>
    <td bgcolor="#245f95" style="padding:13px 22px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Ver novidades</a>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'event',
                'name'      => 'Convite para evento',
                'category'  => 'event',
                'subject'   => 'Está convidado(a): evento {{company_name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Capa do evento', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<p style="margin:0 0 8px;color:#245f95;font-size:12px;font-weight:700;letter-spacing:1.4px;text-transform:uppercase;">Convite</p>
<h1 style="margin:0 0 16px;color:#111827;font-size:28px;line-height:1.25;">Está convidado(a), {{name}}</h1>
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">A {{company_name}} tem o prazer de o(a) convidar para o nosso próximo evento. Será uma oportunidade para conhecer a equipa, acompanhar demonstrações e esclarecer dúvidas.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
  <tr>
    <td width="110" style="padding:12px 0;color:#64748b;font-size:13px;font-weight:700;">Data</td>
    <td style="padding:12px 0;color:#273241;font-size:14px;">[edite]</td>
  </tr>
  <tr>
    <td width="110" style="padding:12px 0;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;font-weight:700;">Hora</td>
    <td style="padding:12px 0;border-top:1px solid #e2e8f0;color:#273241;font-size:14px;">[edite]</td>
  </tr>
  <tr>
    <td width="110" style="padding:12px 0;border-top:1px solid #e2e8f0;color:#64748b;font-size:13px;font-weight:700;">Local</td>
    <td style="padding:12px 0;border-top:1px solid #e2e8f0;color:#273241;font-size:14px;">[edite]</td>
  </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 6px;">
  <tr>
    <td bgcolor="#245f95" style="padding:13px 22px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Confirmar presença</a>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'seasonal-staycation',
                'name'      => 'Campanha sazonal visual',
                'category'  => 'seasonal',
                'subject'   => 'Uma proposta especial para esta época, {{name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem principal', 'type' => 'image'],
                    ['key' => 'feature_image', 'label' => 'Imagem secundária', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f7efe6;margin:0 0 28px;">
  <tr>
    <td style="padding:34px 28px;text-align:center;">
      <p style="margin:0 0 10px;color:#9a5b24;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">Edição sazonal</p>
      <h1 style="margin:0;color:#432818;font-size:34px;line-height:1.15;">Planos tranquilos para aproveitar melhor a sua semana</h1>
      <p style="margin:16px auto 0;color:#68462f;font-size:15px;line-height:1.65;max-width:430px;">Olá {{name}}, preparámos uma seleção especial da {{company_name}} para quem procura conforto, tempo de qualidade e uma experiência bem cuidada.</p>
    </td>
  </tr>
</table>
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 28px;">
  <tr>
    <td style="padding:0 18px 0 0;vertical-align:top;width:50%;">
      <h2 style="margin:0 0 10px;color:#111827;font-size:20px;line-height:1.3;">Oferta principal</h2>
      <p style="margin:0;color:#475569;font-size:14px;line-height:1.65;">Descreva aqui o pacote, serviço ou benefício mais importante da campanha.</p>
    </td>
    <td style="padding:0 0 0 18px;vertical-align:top;width:50%;border-left:1px solid #e2e8f0;">
      <h2 style="margin:0 0 10px;color:#111827;font-size:20px;line-height:1.3;">Porque escolher</h2>
      <p style="margin:0;color:#475569;font-size:14px;line-height:1.65;">Use este espaço para destacar confiança, conveniência ou um diferencial claro.</p>
    </td>
  </tr>
</table>
<img src="{{feature_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 24px;display:block;" />
<table role="presentation" cellpadding="0" cellspacing="0" align="center" style="margin:30px auto 4px;">
  <tr>
    <td bgcolor="#7c3f1d" style="padding:14px 26px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Ver proposta</a>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'product-launch',
                'name'      => 'Lançamento de produto',
                'category'  => 'marketing',
                'subject'   => 'Novo lançamento da {{company_name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem do produto', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<p style="margin:0 0 8px;color:#245f95;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">Novo lançamento</p>
<h1 style="margin:0 0 18px;color:#0f172a;font-size:32px;line-height:1.18;">Apresentamos uma nova forma de avançar com a {{company_name}}</h1>
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<p style="margin:0 0 22px;color:#273241;font-size:15px;line-height:1.65;">Olá {{name}}, criámos esta novidade para simplificar o seu dia a dia e entregar resultados com mais rapidez, clareza e controlo.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 26px;background:#f8fafc;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;">
  <tr>
    <td style="padding:18px 16px;width:33.33%;vertical-align:top;">
      <p style="margin:0 0 6px;color:#111827;font-size:15px;font-weight:700;">01. Mais rápido</p>
      <p style="margin:0;color:#64748b;font-size:13px;line-height:1.55;">Explique o primeiro benefício.</p>
    </td>
    <td style="padding:18px 16px;width:33.33%;vertical-align:top;border-left:1px solid #e2e8f0;">
      <p style="margin:0 0 6px;color:#111827;font-size:15px;font-weight:700;">02. Mais claro</p>
      <p style="margin:0;color:#64748b;font-size:13px;line-height:1.55;">Explique o segundo benefício.</p>
    </td>
    <td style="padding:18px 16px;width:33.33%;vertical-align:top;border-left:1px solid #e2e8f0;">
      <p style="margin:0 0 6px;color:#111827;font-size:15px;font-weight:700;">03. Mais controlo</p>
      <p style="margin:0;color:#64748b;font-size:13px;line-height:1.55;">Explique o terceiro benefício.</p>
    </td>
  </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 4px;">
  <tr>
    <td bgcolor="#245f95" style="padding:14px 24px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Conhecer lançamento</a>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'commerce-showcase',
                'name'      => 'Vitrine comercial',
                'category'  => 'sales',
                'subject'   => 'Selecionámos estas opções para si, {{name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Banner principal', 'type' => 'image'],
                    ['key' => 'feature_image', 'label' => 'Imagem de destaque', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#0f172a;margin:0 0 26px;">
  <tr>
    <td style="padding:32px 28px;text-align:center;">
      <p style="margin:0 0 10px;color:#93c5fd;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">Seleção especial</p>
      <h1 style="margin:0;color:#ffffff;font-size:32px;line-height:1.18;">Produtos e serviços em destaque</h1>
      <p style="margin:14px auto 0;color:#cbd5e1;font-size:15px;line-height:1.6;max-width:430px;">Olá {{name}}, veja uma seleção pensada para as necessidades atuais dos nossos clientes.</p>
    </td>
  </tr>
</table>
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 24px;display:block;" />
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;">
  <tr>
    <td style="padding:0 18px 0 0;width:48%;vertical-align:top;">
      <img src="{{feature_image}}" width="260" alt="" style="width:100%;height:auto;margin:0 0 12px;display:block;" />
      <p style="margin:0 0 6px;color:#111827;font-weight:700;font-size:15px;">Destaque comercial</p>
      <p style="margin:0;color:#64748b;font-size:13px;line-height:1.55;">Edite este texto com o produto ou serviço principal.</p>
    </td>
    <td style="padding:0 0 0 18px;width:52%;vertical-align:top;">
      <h2 style="margin:0 0 12px;color:#111827;font-size:22px;line-height:1.3;">Uma proposta mais direta para decidir melhor</h2>
      <p style="margin:0 0 16px;color:#273241;font-size:14px;line-height:1.65;">Use esta secção para explicar valor, preço, disponibilidade, condições ou próximos passos.</p>
      <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
          <td bgcolor="#16a34a" style="padding:12px 20px;">
            <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:13px;">Pedir proposta</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
HTML,
            ],
            [
                'slug'      => 'customer-success-digest',
                'name'      => 'Resumo para clientes',
                'category'  => 'newsletter',
                'subject'   => 'O seu resumo da {{company_name}}',
                'thumbnail' => null,
                'placeholders' => [
                    ['key' => 'hero_image', 'label' => 'Imagem de abertura', 'type' => 'image'],
                ],
                'body_html' => <<<'HTML'
<img src="{{hero_image}}" width="572" alt="" style="width:100%;max-width:572px;height:auto;margin:0 0 26px;display:block;" />
<p style="margin:0 0 8px;color:#245f95;font-size:12px;font-weight:700;letter-spacing:1.6px;text-transform:uppercase;">Resumo do cliente</p>
<h1 style="margin:0 0 16px;color:#111827;font-size:30px;line-height:1.2;">Olá {{name}}, aqui está o que importa esta semana</h1>
<p style="margin:0 0 24px;color:#273241;font-size:15px;line-height:1.65;">Organizámos os principais pontos, próximos passos e recomendações para manter a sua equipa alinhada.</p>
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:0 0 24px;border-top:1px solid #e2e8f0;">
  <tr>
    <td style="padding:16px 0;border-bottom:1px solid #e2e8f0;">
      <p style="margin:0 0 6px;color:#111827;font-weight:700;font-size:15px;">1. Resultado principal</p>
      <p style="margin:0;color:#475569;font-size:14px;line-height:1.6;">Substitua por uma métrica, atualização ou decisão importante.</p>
    </td>
  </tr>
  <tr>
    <td style="padding:16px 0;border-bottom:1px solid #e2e8f0;">
      <p style="margin:0 0 6px;color:#111827;font-weight:700;font-size:15px;">2. Próximo passo recomendado</p>
      <p style="margin:0;color:#475569;font-size:14px;line-height:1.6;">Explique claramente o que o cliente deve fazer a seguir.</p>
    </td>
  </tr>
  <tr>
    <td style="padding:16px 0;border-bottom:1px solid #e2e8f0;">
      <p style="margin:0 0 6px;color:#111827;font-weight:700;font-size:15px;">3. Apoio da equipa</p>
      <p style="margin:0;color:#475569;font-size:14px;line-height:1.6;">Inclua uma linha direta para contacto ou acompanhamento.</p>
    </td>
  </tr>
</table>
<table role="presentation" cellpadding="0" cellspacing="0" style="margin:28px 0 4px;">
  <tr>
    <td bgcolor="#245f95" style="padding:13px 22px;">
      <a href="#" style="display:inline-block;color:#ffffff;text-decoration:none;font-weight:700;font-size:14px;">Ver detalhes</a>
    </td>
  </tr>
</table>
HTML,
            ],
        ];
    }
}
