<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f3f4f6; padding: 40px 16px; }
        .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
        .header { background: #4f46e5; padding: 32px; }
        .header h1 { color: #ffffff; font-size: 22px; font-weight: 700; letter-spacing: -0.3px; }
        .header p { color: #c7d2fe; font-size: 13px; margin-top: 4px; }
        .greeting { padding: 28px 32px 0; color: #374151; font-size: 15px; }
        .greeting p { margin-bottom: 8px; }
        .body { padding: 20px 32px 36px; color: #374151; line-height: 1.7; font-size: 15px; }
        .footer { background: #f9fafb; padding: 20px 32px; border-top: 1px solid #e5e7eb; }
        .footer p { font-size: 12px; color: #9ca3af; }
        .unsubscribe { color: #9ca3af; text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Khuma CRM</h1>
            <p>{{ $campaign->name }}</p>
        </div>
        <div class="greeting">
            <p>Olá <strong>{{ $clientName }}</strong>,</p>
        </div>
        <div class="body">
            {!! $campaign->body_html !!}
        </div>
        <div class="footer">
            <p>Enviado via Khuma CRM &middot; Campanha: {{ $campaign->name }}</p>
        </div>
    </div>
</body>
</html>
