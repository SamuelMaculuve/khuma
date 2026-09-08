<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensagem - Khuma CRM</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; padding: 40px 16px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .header { background: #4f46e5; padding: 32px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p { color: #c7d2fe; font-size: 13px; margin-top: 4px; }
        .body { padding: 36px 32px; color: #374151; line-height: 1.7; }
        .body p { margin-bottom: 16px; font-size: 15px; }
        .message-box { background: #f8fafc; border-left: 4px solid #4f46e5; border-radius: 4px; padding: 16px 20px; margin: 24px 0; font-size: 15px; color: #1f2937; white-space: pre-wrap; }
        .signature { margin-top: 24px; padding-top: 20px; border-top: 1px solid #e5e7eb; font-size: 14px; color: #6b7280; }
        .signature strong { color: #374151; }
        .footer { background: #f9fafb; padding: 20px 32px; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 12px; color: #9ca3af; }
        .badge { display: inline-block; background: #ede9fe; color: #6d28d9; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 4px; margin-right: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Khuma CRM</h1>
            <p>Mensagem da equipa comercial</p>
        </div>
        <div class="body">
            <p>Olá <strong>{{ $clientName }}</strong>,</p>
            <p>Recebeu uma nova mensagem:</p>
            <div class="message-box">{{ $messageContent }}</div>
            <div class="signature">
                <p>Com os melhores cumprimentos,</p>
                <p><strong>{{ $agentName }}</strong></p>
            </div>
        </div>
        <div class="footer">
            <p><span class="badge">Lead #{{ $leadReference }}</span> Enviado via Khuma CRM &middot; Responda a este email para continuar a conversa.</p>
        </div>
    </div>
</body>
</html>
