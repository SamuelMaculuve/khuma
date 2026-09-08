@php
    $brandName = $companyName ?: config('app.name', 'Khuma CRM');
    $preheader = $campaign->preheader ?: trim(strip_tags($bodyHtml));
    $preheader = preg_replace('/\s+/', ' ', $preheader);
    $preheader = \Illuminate\Support\Str::limit($preheader, 140);
@endphp
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $campaign->subject }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        table { border-collapse: collapse !important; }
        img { -ms-interpolation-mode: bicubic; border: 0; display: block; height: auto; line-height: 100%; max-width: 100%; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background: #f4f6f8; color: #1f2937; font-family: Arial, Helvetica, sans-serif; }
        .email-shell { width: 100%; background: #f4f6f8; }
        .email-container { width: 100%; max-width: 640px; background: #ffffff; border: 1px solid #e2e8f0; }
        .email-body { color: #273241; font-size: 15px; line-height: 1.65; }
        .email-body p { margin: 0 0 16px; }
        .email-body h1 { color: #111827; font-size: 28px; line-height: 1.25; margin: 0 0 16px; }
        .email-body h2 { color: #111827; font-size: 22px; line-height: 1.3; margin: 0 0 14px; }
        .email-body h3 { color: #111827; font-size: 17px; line-height: 1.35; margin: 24px 0 10px; }
        .email-body ul, .email-body ol { margin: 0 0 18px 22px; padding: 0; }
        .email-body li { margin: 0 0 8px; }
        .email-body a { color: #245f95; text-decoration: underline; }
        .email-body .button, .email-body a.button { background: #245f95; color: #ffffff !important; display: inline-block; font-weight: 700; padding: 13px 22px; text-decoration: none; }
        .email-body hr { border: 0; border-top: 1px solid #e2e8f0; margin: 24px 0; }
        @media screen and (max-width: 640px) {
            .outer-padding { padding: 12px !important; }
            .email-header, .email-body, .email-footer { padding-left: 22px !important; padding-right: 22px !important; }
            .email-body h1 { font-size: 24px !important; }
            .email-body h2 { font-size: 20px !important; }
        }
    </style>
</head>
<body>
    <div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">
        {{ $preheader }}
    </div>

    <table role="presentation" class="email-shell" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center" class="outer-padding" style="padding: 28px 14px;">
                <table role="presentation" class="email-container" width="640" cellpadding="0" cellspacing="0">
                    <tr>
                        <td class="email-header" style="padding: 28px 34px 18px; border-bottom: 3px solid #245f95;">
                            <div style="color:#0f172a;font-size:20px;font-weight:700;line-height:1.2;">
                                {{ $brandName }}
                            </div>
                            <div style="color:#64748b;font-size:12px;line-height:1.5;margin-top:6px;">
                                {{ $campaign->name }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="email-body" style="padding: 32px 34px 36px;">
                            {!! $bodyHtml !!}
                        </td>
                    </tr>
                    <tr>
                        <td class="email-footer" style="padding: 18px 34px 24px; background:#f8fafc; border-top:1px solid #e2e8f0;">
                            <p style="margin:0;color:#64748b;font-size:12px;line-height:1.6;">
                                {{ $brandName }}
                            </p>
                            <p style="margin:4px 0 0;color:#94a3b8;font-size:11px;line-height:1.6;">
                                Enviado via Khuma CRM &middot; Campanha: {{ $campaign->name }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
