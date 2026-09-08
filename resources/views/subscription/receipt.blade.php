<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <title>Recibo {{ $payment->transaction_reference ?? $payment->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 p-6 text-slate-900">
    <main class="mx-auto max-w-2xl rounded-xl bg-white p-8 shadow-sm">
        <div class="flex items-start justify-between border-b border-slate-200 pb-6">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#2c6fad]">Khuma CRM</p>
                <h1 class="mt-2 text-2xl font-bold">Recibo de pagamento</h1>
            </div>
            <button onclick="window.print()" class="rounded-lg bg-[#2c6fad] px-4 py-2 text-sm font-bold text-white print:hidden">Imprimir</button>
        </div>

        <dl class="mt-6 grid gap-4 sm:grid-cols-2">
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Empresa</dt>
                <dd class="mt-1 text-sm font-bold">{{ $payment->subscription?->company?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Plano</dt>
                <dd class="mt-1 text-sm font-bold">{{ $payment->subscription?->plan?->name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Metodo</dt>
                <dd class="mt-1 text-sm font-bold uppercase">{{ $payment->method }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Referencia</dt>
                <dd class="mt-1 text-sm font-bold">{{ $payment->transaction_reference ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Data</dt>
                <dd class="mt-1 text-sm font-bold">{{ $payment->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs font-semibold uppercase text-slate-500">Estado</dt>
                <dd class="mt-1 text-sm font-bold">{{ ucfirst($payment->status) }}</dd>
            </div>
        </dl>

        <div class="mt-8 rounded-lg bg-slate-50 p-5">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-600">Total pago</span>
                <span class="text-2xl font-bold text-slate-950">{{ number_format($payment->amount, 2) }} MZN</span>
            </div>
        </div>
    </main>
</body>
</html>
