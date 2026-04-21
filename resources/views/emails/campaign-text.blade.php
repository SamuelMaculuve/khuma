Olá {{ $clientName }},

{{ $campaign->body_text ?? strip_tags($campaign->body_html) }}

---
Enviado via Khuma CRM
Campanha: {{ $campaign->name }}
