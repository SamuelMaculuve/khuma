<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo #{{ $payment->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 13px;
            color: #1a1a2e;
            background: #fff;
            padding: 48px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 32px;
            border-bottom: 2px solid #059669;
            margin-bottom: 36px;
        }
        .brand { font-size: 22px; font-weight: 700; color: #059669; letter-spacing: -0.5px; }
        .brand-sub { font-size: 11px; color: #6b7280; margin-top: 3px; }
        .badge {
            background: #ecfdf5;
            color: #065f46;
            border: 1px solid #6ee7b7;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Title */
        .title-block { margin-bottom: 28px; }
        .title { font-size: 18px; font-weight: 700; color: #111827; }
        .subtitle { font-size: 12px; color: #9ca3af; margin-top: 4px; }

        /* Info Grid */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 32px;
        }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .info-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.8px; color: #9ca3af; font-weight: 600; margin-bottom: 4px; }
        .info-value { font-size: 13px; font-weight: 600; color: #111827; }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        thead tr { background: #f9fafb; }
        th {
            text-align: left;
            padding: 12px 16px;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #6b7280;
            font-weight: 600;
            border-bottom: 1px solid #e5e7eb;
        }
        td { padding: 14px 16px; border-bottom: 1px solid #f3f4f6; font-size: 13px; }
        .amount-col { text-align: right; font-weight: 600; }

        /* Totals */
        .totals { margin-left: auto; width: 280px; }
        .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #6b7280; }
        .total-row.grand { border-top: 2px solid #059669; margin-top: 8px; padding-top: 12px; font-size: 15px; font-weight: 700; color: #059669; }

        /* Ref box */
        .ref-box {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 16px 20px;
            margin-top: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .ref-label { font-size: 11px; color: #9ca3af; text-transform: uppercase; letter-spacing: 0.5px; }
        .ref-value { font-size: 14px; font-weight: 700; font-family: monospace; color: #111827; margin-top: 3px; }

        /* Footer */
        .footer {
            margin-top: 48px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div>
        <div class="brand">{{ config('app.name') }}</div>
        <div class="brand-sub">Comprovativo de Pagamento</div>
    </div>
    <div class="badge">
        @if ($payment->status === 'paid') ✓ Pago
        @elseif ($payment->status === 'pending') ⏳ Pendente
        @else ✕ Falhado
        @endif
    </div>
</div>

{{-- Title --}}
<div class="title-block">
    <div class="title">Factura / Recibo #{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
    <div class="subtitle">Emitido em {{ $payment->created_at->format('d \d\e F \d\e Y, H:i') }}</div>
</div>

{{-- Info --}}
<div class="info-grid">
    <div class="info-col">
        <div style="margin-bottom: 16px;">
            <div class="info-label">Cliente</div>
            <div class="info-value">{{ $payment->user->name }}</div>
            <div style="font-size:12px; color:#6b7280; margin-top:2px;">{{ $payment->user->email }}</div>
        </div>
        <div>
            <div class="info-label">Telefone M-Pesa</div>
            <div class="info-value">+258 {{ $payment->phone }}</div>
        </div>
    </div>
    <div class="info-col" style="text-align: right;">
        <div style="margin-bottom: 16px;">
            <div class="info-label">Método</div>
            <div class="info-value">M-Pesa</div>
        </div>
        <div>
            <div class="info-label">Data de Pagamento</div>
            <div class="info-value">{{ $payment->created_at->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Items --}}
<table>
    <thead>
    <tr>
        <th>Descrição</th>
        <th>Período</th>
        <th class="amount-col" style="text-align:right;">Valor</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <strong>Plano {{ $payment->subscription->plan->name }}</strong><br>
            <span style="font-size:11px; color:#9ca3af;">Subscrição mensal</span>
        </td>
        <td style="color:#6b7280; font-size:12px;">
            {{ $payment->subscription->start_date?->format('d/m/Y') }}
            — {{ $payment->subscription->end_date?->format('d/m/Y') }}
        </td>
        <td class="amount-col">{{ number_format($payment->amount / 1.16, 2) }} MZN</td>
    </tr>
    </tbody>
</table>

{{-- Totals --}}
<div class="totals">
    <div class="total-row">
        <span>Subtotal</span>
        <span>{{ number_format($payment->amount / 1.16, 2) }} MZN</span>
    </div>
    <div class="total-row">
        <span>IVA (16%)</span>
        <span>{{ number_format($payment->amount - ($payment->amount / 1.16), 2) }} MZN</span>
    </div>
    <div class="total-row grand">
        <span>TOTAL</span>
        <span>{{ number_format($payment->amount, 2) }} MZN</span>
    </div>
</div>

{{-- Reference --}}
@if ($payment->transaction_reference)
    <div class="ref-box">
        <div>
            <div class="ref-label">Referência M-Pesa</div>
            <div class="ref-value">{{ $payment->transaction_reference }}</div>
        </div>
        <div style="text-align:right;">
            <div class="ref-label">Nº do Documento</div>
            <div class="ref-value">#{{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}</div>
        </div>
    </div>
@endif

{{-- Footer --}}
<div class="footer">
    <p>{{ config('app.name') }} — {{ config('app.url') }}</p>
    <p style="margin-top:4px;">Este documento é um comprovativo válido de pagamento gerado automaticamente.</p>
</div>

</body>
</html>
