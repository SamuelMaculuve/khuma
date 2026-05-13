<x-app-layout>

    <div class="p-6 space-y-6">

        @unless(auth()->user()->hasActiveSubscription())
            <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <span>Escolha uma subscrição para desbloquear CRM, WhatsApp, email marketing e outros módulos.</span>
                    <a href="{{ route('subscription.plans') }}" class="inline-flex items-center justify-center rounded-md bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700">
                        Ver planos
                    </a>
                </div>
            </div>
        @endunless

        {{-- KPI Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="stat-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total Leads</span>
                    <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $total }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $totalNew }} novos</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ganhos</span>
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-emerald-600">{{ $totalWon }}</p>
                <p class="text-xs text-gray-400 mt-1">Taxa: {{ $conversionRate }}%</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clientes</span>
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalClients }}</p>
                <p class="text-xs text-gray-400 mt-1">registados</p>
            </div>

            <div class="stat-card">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium text-gray-500 uppercase tracking-wide">Campanhas</span>
                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                </div>
                <p class="text-3xl font-bold text-gray-900">{{ $totalCampaigns }}</p>
                <p class="text-xs text-gray-400 mt-1">email marketing</p>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Pipeline --}}
            <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="font-semibold text-gray-900">Pipeline de Leads</h3>
                    <a href="{{ route('leads.all') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver kanban →</a>
                </div>

                @php
                    $stages = [
                        'new'         => ['label' => 'Novos',       'color' => 'bg-slate-400'],
                        'contacted'   => ['label' => 'Contactados', 'color' => 'bg-blue-400'],
                        'qualified'   => ['label' => 'Qualificados','color' => 'bg-indigo-400'],
                        'proposal'    => ['label' => 'Proposta',    'color' => 'bg-violet-400'],
                        'negotiation' => ['label' => 'Negociação',  'color' => 'bg-amber-400'],
                        'won'         => ['label' => 'Ganhos',      'color' => 'bg-emerald-500'],
                        'lost'        => ['label' => 'Perdidos',    'color' => 'bg-red-400'],
                    ];
                    $maxVal = max(1, ...array_values($pipeline));
                @endphp

                <div class="space-y-3">
                    @foreach($stages as $key => $stage)
                        <div class="flex items-center gap-3">
                            <span class="text-xs text-gray-500 w-20 shrink-0 text-right">{{ $stage['label'] }}</span>
                            <div class="flex-1 h-7 bg-gray-100 rounded-lg overflow-hidden">
                                <div class="{{ $stage['color'] }} h-full rounded-lg flex items-center px-2 transition-all duration-500"
                                     style="width: {{ $pipeline[$key] > 0 ? max(4, round(($pipeline[$key] / $maxVal) * 100)) : 0 }}%">
                                    @if($pipeline[$key] > 0)
                                        <span class="text-white text-xs font-semibold">{{ $pipeline[$key] }}</span>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs font-medium text-gray-700 w-6 shrink-0">{{ $pipeline[$key] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Quick actions + report --}}
            <div class="space-y-4">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Acções Rápidas</h3>
                    <div class="space-y-2">
                        <a href="{{ route('leads.all') }}"
                           class="flex items-center gap-3 px-4 py-3 bg-indigo-50 hover:bg-indigo-100 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="text-sm font-medium text-indigo-700">Novo Lead</span>
                        </a>
                        <a href="{{ route('email-campaigns.index') }}"
                           class="flex items-center gap-3 px-4 py-3 bg-purple-50 hover:bg-purple-100 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span class="text-sm font-medium text-purple-700">Nova Campanha</span>
                        </a>
                        <a href="{{ route('settings.index') }}?tab=teams"
                           class="flex items-center gap-3 px-4 py-3 bg-blue-50 hover:bg-blue-100 rounded-xl transition-colors">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-sm font-medium text-blue-700">Gerir Equipas</span>
                        </a>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-2xl p-6 text-white">
                    <h3 class="font-semibold mb-1">Relatórios</h3>
                    <p class="text-indigo-200 text-xs mb-4">Exportar dados para análise</p>
                    <a href="{{ route('dashboard.report') }}"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-white text-indigo-700 text-sm font-semibold rounded-xl hover:bg-indigo-50 transition-colors w-full justify-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Descarregar CSV
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent leads --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-900">Leads Recentes</h3>
                <a href="{{ route('leads.all') }}" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver todos →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50 text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-3 text-left">Lead</th>
                            <th class="px-6 py-3 text-left">Cliente</th>
                            <th class="px-6 py-3 text-left">Estado</th>
                            <th class="px-6 py-3 text-left">Fonte</th>
                            <th class="px-6 py-3 text-left">Data</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentLeads as $lead)
                            @php
                                $statusColor = match($lead->status) {
                                    'new'         => 'bg-slate-100 text-slate-700',
                                    'contacted'   => 'bg-blue-100 text-blue-700',
                                    'qualified'   => 'bg-indigo-100 text-indigo-700',
                                    'proposal'    => 'bg-violet-100 text-violet-700',
                                    'negotiation' => 'bg-amber-100 text-amber-700',
                                    'won'         => 'bg-emerald-100 text-emerald-700',
                                    'lost'        => 'bg-red-100 text-red-700',
                                    default       => 'bg-gray-100 text-gray-700',
                                };
                                $statusLabel = match($lead->status) {
                                    'new' => 'Novo', 'contacted' => 'Contactado',
                                    'qualified' => 'Qualificado', 'proposal' => 'Proposta',
                                    'negotiation' => 'Negociação', 'won' => 'Ganho', 'lost' => 'Perdido',
                                    default => $lead->status,
                                };
                            @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3">
                                    <a href="{{ route('lead.show', $lead->id) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">
                                        {{ $lead->title }}
                                    </a>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $lead->reference }}</p>
                                </td>
                                <td class="px-6 py-3 text-sm text-gray-700">{{ optional($lead->client)->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $lead->source ?? '—' }}</td>
                                <td class="px-6 py-3 text-xs text-gray-500">{{ $lead->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">Nenhum lead ainda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</x-app-layout>
