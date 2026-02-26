<div class="min-h-screen bg-gray-50">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Pagamentos & Facturas</h1>
                    <p class="text-sm text-gray-500 mt-1">Histórico completo de todos os seus pagamentos</p>
                </div>
                <a href="{{ route('profile.edit') }}"
                   class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 border border-gray-200 rounded-xl px-4 py-2 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Voltar ao Perfil
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ── Stats ───────────────────────────────────────────────────────── --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            @foreach ([
                ['Total Pago',  number_format($totals->total_paid ?? 0, 2) . ' MZN', 'bg-emerald-50', 'text-emerald-700', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Pagamentos',  $totals->total_count ?? 0,                            'bg-blue-50',    'text-blue-700',    'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z'],
                ['Pendentes',   $totals->pending_count ?? 0,                          'bg-amber-50',   'text-amber-700',   'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ['Falhados',    $totals->failed_count ?? 0,                           'bg-red-50',     'text-red-700',     'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ] as [$label, $value, $bg, $color, $icon])
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 {{ $bg }} rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $color }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500">{{ $label }}</span>
                    </div>
                    <p class="text-xl font-bold text-gray-900">{{ $value }}</p>
                </div>
            @endforeach
        </div>

        {{-- ── Filters ─────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Pesquisar por referência ou telefone..."
                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition"
                    />
                </div>
                <select
                    wire:model.live="statusFilter"
                    class="border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white"
                >
                    <option value="">Todos os estados</option>
                    <option value="paid">Pagos</option>
                    <option value="pending">Pendentes</option>
                    <option value="failed">Falhados</option>
                </select>
            </div>
        </div>

        {{-- ── Tabela ───────────────────────────────────────────────────────── --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @if ($payments->count())
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                        <tr class="border-b border-gray-100 bg-gray-50">
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Data</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Plano</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Referência</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Telefone</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Valor</th>
                            <th class="text-left px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Estado</th>
                            <th class="text-right px-6 py-4 font-semibold text-gray-500 text-xs uppercase tracking-wider">Factura</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                        @foreach ($payments as $payment)
                            <tr class="hover:bg-gray-50 transition-colors group">

                                <td class="px-6 py-4 text-gray-600">
                                    <div class="font-medium">{{ $payment->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $payment->created_at->format('H:i') }}</div>
                                </td>

                                <td class="px-6 py-4 font-semibold text-gray-800">
                                    {{ $payment->subscription->plan->name ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-500 font-mono text-xs">
                                    {{ $payment->transaction_reference ?? '—' }}
                                </td>

                                <td class="px-6 py-4 text-gray-600">
                                    +258 {{ $payment->phone }}
                                </td>

                                <td class="px-6 py-4 font-bold text-gray-800">
                                    {{ number_format($payment->amount, 2) }} MZN
                                </td>

                                <td class="px-6 py-4">
                                    @php
                                        $map = [
                                            'paid'    => ['bg-emerald-100 text-emerald-700', '✓ Pago'],
                                            'pending' => ['bg-amber-100 text-amber-700',     '⏳ Pendente'],
                                            'failed'  => ['bg-red-100 text-red-700',         '✕ Falhado'],
                                        ];
                                        [$cls, $lbl] = $map[$payment->status] ?? ['bg-gray-100 text-gray-600', ucfirst($payment->status)];
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold {{ $cls }}">
                                            {{ $lbl }}
                                        </span>
                                </td>

                                {{-- BOTÃO DOWNLOAD --}}
                                <td class="px-6 py-4 text-right">
                                    @if ($payment->status === 'paid')
                                        <a
                                            href="{{ route('receipt.download', $payment->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-lg px-3 py-2 transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-300">—</span>
                                    @endif
                                </td>

                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $payments->links() }}
                </div>

            @else
                <div class="py-20 text-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                    </div>
                    <p class="font-semibold text-gray-600">Nenhum pagamento encontrado</p>
                    <p class="text-gray-400 text-sm mt-1">
                        @if ($search || $statusFilter)
                            Tente ajustar os filtros de pesquisa
                        @else
                            Os seus pagamentos aparecerão aqui após a primeira subscrição
                        @endif
                    </p>
                </div>
            @endif
        </div>

    </div>
</div>
