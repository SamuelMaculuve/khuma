<div class="min-h-[calc(100vh-44px)] bg-slate-50">
    @php
        $isActive = $subscription?->isActive() ?? false;
        $renewsAt = $subscription?->renews_at;
        $daysLeft = $renewsAt ? max(0, now()->diffInDays($renewsAt, false)) : null;
        $price = $currentPlan?->currentPrice();
        $featureLabels = [
            'crm' => 'CRM e pipeline',
            'whatsapp' => 'WhatsApp',
            'email_campaigns' => 'Email marketing',
            'reports' => 'Relatórios',
            'team_management' => 'Gestão de equipa',
            'call_logs' => 'Registos de chamadas',
            'product_sales' => 'Venda de produtos',
            'bulk_messages' => 'Mensagens em massa',
            'whatsapp_templates' => 'Templates WhatsApp',
            'priority_support' => 'Suporte prioritário',
        ];
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#2c6fad]">Subscrição</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Painel da subscrição</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Monitore o seu plano, pagamentos e funcionalidades disponíveis para a sua conta.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('subscription.plans') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#2c6fad] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-[#245b8e]">
                    <i class="fa-solid fa-arrow-up-right-dots text-xs"></i>
                    {{ $subscription ? 'Atualizar plano' : 'Escolher plano' }}
                </a>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                    <i class="fa-solid fa-chart-line text-xs"></i>
                    Voltar ao CRM
                </a>
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            <section class="lg:col-span-2 overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-100 bg-gradient-to-br from-slate-950 via-[#1f5f8f] to-[#2c6fad] p-6 text-white">
                    <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.22em] text-white/70">Plano atual</p>
                            <h2 class="mt-3 text-3xl font-bold">{{ $currentPlan?->name ?? 'Sem plano ativo' }}</h2>
                            <p class="mt-2 max-w-xl text-sm text-white/75">
                                {{ $currentPlan?->description ?? 'Escolha uma subscrição para desbloquear os módulos pagos do Khuma CRM.' }}
                            </p>
                        </div>
                        <span class="inline-flex w-fit items-center rounded-full px-3 py-1 text-xs font-bold {{ $isActive ? 'bg-emerald-400/15 text-emerald-100 ring-1 ring-emerald-200/30' : 'bg-amber-300/15 text-amber-100 ring-1 ring-amber-200/30' }}">
                            {{ $isActive ? 'Ativa' : ($subscription ? 'Pendente' : 'Sem subscrição') }}
                        </span>
                    </div>

                    <div class="mt-8 grid gap-3 sm:grid-cols-3">
                        <div class="rounded-lg bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-xs text-white/60">Preço mensal</p>
                            <p class="mt-2 text-xl font-bold">{{ $price ? number_format($price->amount, 0) . ' ' . $price->currency : '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-xs text-white/60">Renovação</p>
                            <p class="mt-2 text-xl font-bold">{{ $renewsAt ? $renewsAt->format('d/m/Y') : '-' }}</p>
                        </div>
                        <div class="rounded-lg bg-white/10 p-4 ring-1 ring-white/10">
                            <p class="text-xs text-white/60">Dias restantes</p>
                            <p class="mt-2 text-xl font-bold">{{ $daysLeft !== null ? $daysLeft : '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="mb-4 flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900">Funcionalidades disponíveis</h3>
                        <a href="{{ route('subscription.plans') }}" class="text-sm font-semibold text-[#2c6fad] hover:text-[#245b8e]">Comparar planos</a>
                    </div>

                    @if($currentPlan)
                        <div class="grid gap-3 sm:grid-cols-2">
                            @foreach($currentPlan->features as $feature)
                                @if($currentPlan->hasFeature($feature->feature_key))
                                    <div class="flex items-center gap-3 rounded-lg border border-slate-100 bg-slate-50 px-4 py-3">
                                        <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                                            <i class="fa-solid fa-check text-xs"></i>
                                        </span>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-800">{{ $featureLabels[$feature->feature_key] ?? str($feature->feature_key)->replace('_', ' ')->title() }}</p>
                                            @if(!in_array($feature->feature_value, ['1', 'true'], true))
                                                <p class="text-xs text-slate-500">Limite: {{ $feature->feature_value }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg border border-dashed border-slate-300 bg-slate-50 p-6 text-center">
                            <p class="text-sm font-semibold text-slate-800">Nenhuma funcionalidade ativa ainda.</p>
                            <p class="mt-1 text-sm text-slate-500">Escolha um plano para desbloquear os módulos da plataforma.</p>
                        </div>
                    @endif
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <h3 class="text-base font-bold text-slate-900">Próximo passo</h3>
                        <i class="fa-solid fa-bolt text-[#2c6fad]"></i>
                    </div>
                    <p class="mt-3 text-sm text-slate-600">
                        {{ $isActive ? 'O seu plano está ativo. Pode atualizar quando precisar de mais capacidade.' : 'Ative uma subscrição para remover bloqueios dos módulos pagos.' }}
                    </p>
                    <a href="{{ route('subscription.plans') }}" class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-slate-950 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800">
                        <i class="fa-solid fa-layer-group text-xs"></i>
                        Ver planos
                    </a>
                </section>

                <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm">
                    <h3 class="text-base font-bold text-slate-900">Planos disponíveis</h3>
                    <div class="mt-4 space-y-3">
                        @foreach($plans as $plan)
                            @php $planPrice = $plan->currentPrice(); @endphp
                            <a href="{{ route('subscription.checkout', $plan) }}" class="block rounded-lg border px-4 py-3 transition {{ $currentPlan?->id === $plan->id ? 'border-[#2c6fad] bg-blue-50' : 'border-slate-200 hover:border-[#2c6fad] hover:bg-slate-50' }}">
                                <div class="flex items-center justify-between gap-3">
                                    <p class="text-sm font-bold text-slate-900">{{ $plan->name }}</p>
                                    <p class="text-xs font-semibold text-slate-500">{{ $planPrice ? number_format($planPrice->amount, 0) . ' ' . $planPrice->currency : 'Sob consulta' }}</p>
                                </div>
                                <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $plan->description }}</p>
                            </a>
                        @endforeach
                    </div>
                </section>
            </aside>
        </div>

        <section class="mt-5 rounded-lg border border-slate-200 bg-white shadow-sm">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <h3 class="text-base font-bold text-slate-900">Histórico de pagamentos</h3>
                <span class="text-xs font-medium text-slate-400">{{ $payments->count() }} registos</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Data</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Metodo</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Referencia</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Valor</th>
                            <th class="px-5 py-3 text-left text-xs font-bold uppercase tracking-wide text-slate-500">Estado</th>
                            <th class="px-5 py-3 text-right text-xs font-bold uppercase tracking-wide text-slate-500">Recibo</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="px-5 py-3 text-sm text-slate-600">{{ $payment->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-5 py-3 text-sm font-semibold uppercase text-slate-700">{{ $payment->method }}</td>
                                <td class="px-5 py-3 text-sm text-slate-500">{{ $payment->transaction_reference ?? '-' }}</td>
                                <td class="px-5 py-3 text-sm font-bold text-slate-900">{{ number_format($payment->amount, 2) }} MZN</td>
                                <td class="px-5 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-bold {{ $payment->status === 'paid' ? 'bg-emerald-100 text-emerald-700' : ($payment->status === 'failed' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('subscription.receipts.show', $payment) }}" target="_blank" class="text-sm font-semibold text-[#2c6fad] hover:text-[#245b8e]">Abrir</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-500">Ainda nao existem pagamentos registados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
