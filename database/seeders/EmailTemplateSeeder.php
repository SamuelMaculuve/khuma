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
<p>Olá <strong>{{name}}</strong>,</p>
<p>Escreva aqui a sua mensagem. Pode formatar texto, adicionar links e imagens.</p>
<p>Cumprimentos,<br>{{company_name}}</p>
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
<div style="text-align:center;margin-bottom:24px">
  <img src="{{hero_image}}" alt="" style="max-width:100%;border-radius:12px" />
</div>
<h2 style="color:#111827;margin-bottom:12px">Olá {{name}}, bem-vindo!</h2>
<p style="color:#374151;line-height:1.7">Estamos muito felizes por o(a) ter connosco. Nos próximos dias vamos partilhar tudo o que precisa saber para tirar o máximo proveito da {{company_name}}.</p>
<p style="text-align:center;margin:28px 0">
  <a href="#" style="background:#4f46e5;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600">Começar agora</a>
</p>
<p style="color:#6b7280;font-size:13px">Se tiver qualquer dúvida, basta responder a este email.</p>
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
<div style="background:#fef3c7;padding:24px;border-radius:12px;text-align:center;margin-bottom:24px">
  <p style="font-size:13px;color:#92400e;font-weight:600;letter-spacing:1px;text-transform:uppercase">Oferta limitada</p>
  <h1 style="color:#78350f;font-size:30px;margin:8px 0">-30% em tudo</h1>
  <p style="color:#92400e">Apenas até ao fim da semana</p>
</div>
<img src="{{hero_image}}" alt="" style="max-width:100%;border-radius:12px;margin-bottom:20px" />
<p>Olá <strong>{{name}}</strong>,</p>
<p>Preparámos uma oferta exclusiva. Use o código <strong>SAVE30</strong> e poupe 30% em qualquer encomenda.</p>
<p style="text-align:center;margin:28px 0">
  <a href="#" style="background:#dc2626;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:700">Aproveitar agora</a>
</p>
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
<h2 style="color:#111827">Olá {{name}},</h2>
<p style="color:#374151">Aqui estão as novidades deste mês na {{company_name}}.</p>
<img src="{{hero_image}}" alt="" style="max-width:100%;border-radius:12px;margin:20px 0" />
<h3 style="color:#111827;margin-top:24px">Destaque do mês</h3>
<p style="color:#374151;line-height:1.7">Resumo do conteúdo principal. Edite este parágrafo com a sua história.</p>
<hr style="border:none;border-top:1px solid #e5e7eb;margin:24px 0" />
<h3 style="color:#111827">Outras notícias</h3>
<ul style="color:#374151;line-height:1.8">
  <li>Tópico 1 — descreva aqui</li>
  <li>Tópico 2 — descreva aqui</li>
  <li>Tópico 3 — descreva aqui</li>
</ul>
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
<div style="text-align:center">
  <img src="{{hero_image}}" alt="" style="max-width:240px;margin-bottom:24px" />
  <h1 style="color:#111827;font-size:28px">Há já algum tempo...</h1>
  <p style="color:#6b7280;font-size:16px;margin:12px 0 24px">Olá {{name}}, queríamos voltar a falar consigo.</p>
</div>
<p style="color:#374151;line-height:1.7">Lançámos várias novidades desde a última vez que esteve connosco. Volte a passar pela {{company_name}} e descubra o que está diferente.</p>
<p style="text-align:center;margin:32px 0">
  <a href="#" style="background:#4f46e5;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600">Ver novidades</a>
</p>
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
<img src="{{hero_image}}" alt="" style="max-width:100%;border-radius:12px;margin-bottom:24px" />
<h1 style="color:#111827">Está convidado(a), {{name}}!</h1>
<p style="color:#374151;line-height:1.7">Temos o prazer de o(a) convidar para o nosso próximo evento. Será uma ocasião para conhecer a equipa, ver demonstrações ao vivo e tirar todas as suas dúvidas.</p>
<table style="margin:24px 0;color:#374151" cellpadding="6">
  <tr><td><strong>Data:</strong></td><td>[edite]</td></tr>
  <tr><td><strong>Hora:</strong></td><td>[edite]</td></tr>
  <tr><td><strong>Local:</strong></td><td>[edite]</td></tr>
</table>
<p style="text-align:center;margin:28px 0">
  <a href="#" style="background:#4f46e5;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600">Confirmar presença</a>
</p>
HTML,
            ],
        ];
    }
}
