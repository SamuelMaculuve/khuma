<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprovativo de Pagamento</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #1a1a2e;
            background: #ffffff;
            padding: 0;
        }

        /* ── Header ───────────────────────── */
        .header {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 60%, #0f3460 100%);
            color: white;
            padding: 36px 48px;
            position: relative;
            overflow: hidden;
        }

        .header::after {
            content: '';
            position: absolute;
            top: -30px; right: -30px;
            width: 160px; height: 160px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 28px;
        }

        .brand {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .brand span {
            color: #e94560;
        }

        .invoice-badge {
            background: rgba(233, 69, 96, 0.2);
            border: 1px solid rgba(233, 69, 96, 0.4);
            color: #e94560;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .header-meta {
            display: flex;
            gap: 40px;
        }

        .header-meta-item label {
            display: block;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.5;
            margin-bottom: 4px;
        }

        .header-meta-item value {
            display: block;
            font-size: 14px;
            font-weight: 600;
            opacity: 0.95;
        }

        /* ── Body ──────────────────────────── */
        .body {
            padding: 40px 48px;
        }

        /* ── Parties ──────────────────────── */
        .parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 36px;
            gap: 24px;
        }

        .party {
            flex: 1;
        }

        .party-label {
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #e94560;
            margin-bottom: 10px;
            padding-bottom: 6px;
            border-bottom: 2px solid #e94560;
        }

        .party-name {
            font-size: 16px;
            font-weight: 700;
            color: #1a1a2e;
            margin-bottom: 4px;
        }

        .party-detail {
            font-size: 12px;
            color: #666;
            line-height: 1.7;
        }

        /* ── Amount Hero ───────────────────── */
        .amount-hero {
            background: #f8f9ff;
            border: 1px solid #e8eaf6;
            border-left: 4px solid #e94560;
            border-radius: 12px;
            padding: 24px 28px;
            margin-bottom: 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .amount-hero-label {
            font-size: 13px;
            color: #666;
            font-weight: 500;
        }

        .amount-hero-value {
            font-size: 36px;
            font-weight: 800;
            color: #1a1a2e;
            letter-spacing: -1px;
        }

        .amount-hero-currency {
            font-size: 16px;
            font-weight: 600;
            color: #999;
            margin-left: 6px;
        }

        .status-pill {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        /* ── Details Table ─────────────────── */
        .section-title {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #999;
            margin-bottom: 14px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 32px;
        }

        .details-table tr {
            border-bottom: 1px solid #f0f0f0;
        }

        .details-table tr:last-child {
            border-bottom: none;
        }

        .details-table td {
            padding: 11px 0;
            font-size: 13px;
        }

        .details-table td:first-child {
            color: #888;
            width: 50%;
        }

        .details-table td:last-child {
            font-weight: 600;
            color: #1a1a2e;
            text-align: right;
        }

        /* ── Totals ────────────────────────── */
        .totals {
            background: #f8f9ff;
            border-radius: 12px;
            padding: 20px 24px;
            margin-bottom: 32px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            font-size: 13px;
            border-bottom: 1px solid #eeeeee;
        }

        .totals-row:last-child {
            border-bottom: none;
            padding-top: 12px;
            margin-top: 4px;
        }

        .totals-row.total-final {
            font-size: 16px;
            font-weight: 800;
            color: #1a1a2e;
        }

        .totals-row label { color: #666; }
        .totals-row value { font-weight: 600; }

        /* ── Footer ────────────────────────── */
        .footer {
            border-top: 1px solid #eee;
            padding: 24px 48px;
            text-align: center;
        }

        .footer p {
            font-size: 11px;
            color: #bbb;
            line-height: 1.8;
        }

        .footer strong {
            color: #888;
        }

        .verified-stamp {
            display: inline-block;
            border: 2px solid #dcfce7;
            color: #166534;
            background: #f0fdf4;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <div class="header-top">
        <div class="brand">Moz<span>Bot</span></div>
        <div class="invoice-badge">Comprovativo</div>
    </div>
    <div class="header-meta">
        <div class="header-meta-item">
            <label>Número</label>
            <value>{{ $payment->transaction_reference ?? 'SUB-' . str_pad($payment->id, 5, '0', STR_PAD_LEFT) }}</value>
        </div>
        <div class="header-meta-item">
            <label>Data</label>
            <value>{{ $payment->created_at->format('d/m/Y') }}</value>
        </div>
        <div class="header-meta-item">
            <label>Hora</label>
            <value>{{ $payment->created_at->format('H:i') }}</value>
        </div>
    </div>
</div>

{{-- Body --}}
<div class="body">

    {{-- Parties --}}
    <div class="parties">
        <div class="party">
            <div class="party-label">Emitido por</div>
            <div class="party-name">MozBot, Lda.</div>
            <div class="party-detail">
                Maputo, Moçambique<br>
                suporte@mozbot.co.mz<br>
                NUIT: 400123456
            </div>
        </div>
        <div class="party">
            <div class="party-label">Pagador</div>
            <div class="party-name">{{ $payment->user->name }}</div>
            <div class="party-detail">
                {{ $payment->user->email }}<br>
                M-Pesa: +258 {{ $payment->phone }}
            </div>
        </div>
    </div>

    {{-- Amount Hero --}}
    <div class="amount-hero">
        <div>
            <div class="amount-hero-label">Valor Total Pago</div>
            <div>
                <span class="amount-hero-value">{{ number_format($payment->amount, 2) }}</span>
                <span class="amount-hero-currency">MZN</span>
            </div>
        </div>
        <div class="status-pill">✓ Pagamento Confirmado</div>
    </div>

    {{-- Details --}}
    <div class="section-title">Detalhes do Serviço</div>
    <table class="details-table">
        <tr>
            <td>Plano</td>
            <td>{{ $payment->subscription?->plan?->name ?? 'Subscrição' }}</td>
        </tr>
        <tr>
            <td>Período</td>
            <td>
                @if ($payment->subscription)
                    {{ $payment->subscription->start_date->format('d/m/Y') }}
                    — {{ $payment->subscription->end_date->format('d/m/Y') }}
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td>Método de Pagamento</td>
            <td>M-Pesa (+258 {{ $payment->phone }})</td>
        </tr>
        <tr>
            <td>Referência da Transacção</td>
            <td>{{ $payment->transaction_reference ?? '—' }}</td>
        </tr>
    </table>

    {{-- Totals --}}
    @php
        $subtotal = round($payment->amount / 1.16, 2);
        $iva = round($payment->amount - $subtotal, 2);
    @endphp

    <div class="section-title">Discriminação de Valores</div>
    <div class="totals">
        <div class="totals-row">
            <label>Subtotal</label>
            <value>{{ number_format($subtotal, 2) }} MZN</value>
        </div>
        <div class="totals-row">
            <label>IVA (16%)</label>
            <value>{{ number_format($iva, 2) }} MZN</value>
        </div>
        <div class="totals-row total-final">
            <label>Total</label>
            <value>{{ number_format($payment->amount, 2) }} MZN</value>
        </div>
    </div>

</div>

{{-- Footer --}}
<div class="footer">
    <div class="verified-stamp">✓ Documento Verificado</div>
    <p>
        Este comprovativo foi gerado automaticamente e é válido sem assinatura.<br>
        <strong>MozBot, Lda.</strong> · Maputo, Moçambique · suporte@mozbot.co.mz
    </p>
</div>

</body>
</html>
