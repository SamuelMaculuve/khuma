<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- Page header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Configurações</h1>
        <p class="text-sm text-gray-500 mt-1">Gerencie a sua empresa, email e equipas.</p>
    </div>

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 p-1 rounded-xl w-fit">
        @foreach(['general' => 'Geral', 'mail' => 'Email', 'teams' => 'Equipas'] as $key => $label)
            <button wire:click="$set('tab', '{{ $key }}')"
                    class="px-5 py-2 rounded-lg text-sm font-medium transition-all
                           {{ $tab === $key ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- ── GENERAL ─────────────────────────────────────────── --}}
    @if($tab === 'general')
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="px-6 py-4 border-b border-gray-100">
                <h2 class="font-semibold text-gray-900">Informações da Empresa</h2>
            </div>
            <div class="p-6 space-y-4">
                @if(session('success_general'))
                    <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('success_general') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da empresa *</label>
                        <input type="text" wire:model="companyName"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('companyName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" wire:model="companyEmail"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                        <input type="text" wire:model="companyPhone"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                        <input type="text" wire:model="companyAddress"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button wire:click="saveGeneral"
                            class="px-5 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── MAIL ────────────────────────────────────────────── --}}
    @if($tab === 'mail')
        <div class="space-y-4">

            @if(session('success_mail'))
                <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
                    {{ session('success_mail') }}
                </div>
            @endif

            {{-- Email integration status --}}
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm"
                 @if(in_array($mailStatus, ['pending', 'provisioning'])) wire:poll.8s="refreshMailStatus" @endif>
                @php
                    $isReady = $mailStatus === 'ready';
                    $isProvisioning = $mailStatus === 'provisioning';
                    $isPending = $mailStatus === 'pending';
                    $isFailed = $mailStatus === 'failed';
                    $parent = config('services.mail_tenant.parent_domain');
                    $fqdn = $company?->mail_subdomain ? $company->mail_subdomain . '.' . $parent : null;
                    $minutesSinceUpdate = $company?->updated_at ? $company->updated_at->diffInMinutes(now()) : null;
                    $hasStalled = $isProvisioning && $minutesSinceUpdate !== null && $minutesSinceUpdate >= 10;
                    $statusConfig = match ($mailStatus) {
                        'ready' => [
                            'label' => 'Activo',
                            'title' => 'Email dedicado pronto',
                            'body' => 'Os endereços de email da sua empresa já podem receber respostas e enviar campanhas.',
                            'badge' => 'bg-emerald-100 text-emerald-800',
                            'dot' => 'bg-emerald-500',
                            'panel' => 'from-emerald-50 to-white',
                            'icon' => 'fa-circle-check',
                            'iconColor' => 'text-emerald-600 bg-emerald-100',
                        ],
                        'provisioning' => [
                            'label' => 'A configurar',
                            'title' => $hasStalled ? 'A configuração está a demorar mais que o normal' : 'Estamos a configurar o email dedicado',
                            'body' => $hasStalled
                                ? 'O pedido foi enviado, mas não houve atualização recente. Normalmente isto indica que a fila não está a correr ou que uma API externa não respondeu.'
                                : 'Estamos a criar o domínio, caixa de entrada, aliases, DNS e DKIM. Esta página atualiza automaticamente.',
                            'badge' => 'bg-amber-100 text-amber-800',
                            'dot' => 'bg-amber-500 animate-pulse',
                            'panel' => 'from-amber-50 to-white',
                            'icon' => 'fa-circle-notch fa-spin',
                            'iconColor' => 'text-amber-600 bg-amber-100',
                        ],
                        'failed' => [
                            'label' => 'Falhou',
                            'title' => 'Não foi possível configurar o email',
                            'body' => 'Revise os detalhes do erro abaixo e tente novamente depois de corrigir a configuração.',
                            'badge' => 'bg-red-100 text-red-800',
                            'dot' => 'bg-red-500',
                            'panel' => 'from-red-50 to-white',
                            'icon' => 'fa-triangle-exclamation',
                            'iconColor' => 'text-red-600 bg-red-100',
                        ],
                        default => [
                            'label' => 'Não iniciado',
                            'title' => 'Email dedicado ainda não ativado',
                            'body' => 'Ative o email dedicado para usar aliases de equipa, campanhas e respostas ligadas aos leads.',
                            'badge' => 'bg-slate-100 text-slate-700',
                            'dot' => 'bg-slate-400',
                            'panel' => 'from-slate-50 to-white',
                            'icon' => 'fa-envelope',
                            'iconColor' => 'text-slate-500 bg-slate-100',
                        ],
                    };
                @endphp

                <div class="bg-gradient-to-br {{ $statusConfig['panel'] }} p-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">Integração de Email</h2>
                        <p class="text-sm text-gray-500 mt-0.5">O seu endereço de email dedicado para leads e campanhas.</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold
                        {{ $statusConfig['badge'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusConfig['dot'] }}"></span>
                        {{ $statusConfig['label'] }}
                    </span>
                    </div>

                    <div class="mt-6 flex gap-4 rounded-2xl border border-white/70 bg-white/80 p-5 shadow-sm">
                        <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl {{ $statusConfig['iconColor'] }}">
                            <i class="fa-solid {{ $statusConfig['icon'] }}"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900">{{ $statusConfig['title'] }}</h3>
                            <p class="mt-1 text-sm leading-6 text-gray-600">{{ $statusConfig['body'] }}</p>

                            @if($isProvisioning)
                                <div class="mt-4 grid gap-2 sm:grid-cols-4">
                                    @foreach([
                                        ['label' => 'Pedido', 'done' => true],
                                        ['label' => 'Mailcow', 'done' => $company?->mail_inbox_local_part],
                                        ['label' => 'DNS', 'done' => filled($company?->mail_cloudflare_records)],
                                        ['label' => 'Pronto', 'done' => $isReady],
                                    ] as $step)
                                        <div class="rounded-xl border px-3 py-2 {{ $step['done'] ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-amber-200 bg-amber-50 text-amber-800' }}">
                                            <div class="flex items-center gap-2">
                                                <i class="fa-solid {{ $step['done'] ? 'fa-check' : 'fa-clock' }} text-xs"></i>
                                                <span class="text-xs font-semibold">{{ $step['label'] }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($hasStalled)
                                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                                        <p class="font-semibold">Verifique se o worker da fila está ativo.</p>
                                        <p class="mt-1 text-amber-800">Com `QUEUE_CONNECTION=database` ou `redis`, este processo só avança quando `php artisan queue:work` estiver em execução.</p>
                                    </div>
                                @endif
                            @endif

                            @if($isFailed && $company?->mail_provision_error)
                                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-red-700">Erro técnico</p>
                                    <p class="mt-1 break-words text-sm text-red-800">{{ $company->mail_provision_error }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 p-6">

                @if($isReady && $company?->mail_subdomain)
                    @php
                        $emailUses = [
                            ['address' => 'commercial@' . $fqdn, 'label' => 'Leads & CRM',      'icon' => 'fa-handshake',  'color' => 'bg-blue-100 text-blue-600'],
                            ['address' => 'campaign@'   . $fqdn, 'label' => 'Email Marketing',  'icon' => 'fa-bullhorn',   'color' => 'bg-purple-100 text-purple-600'],
                            ['address' => 'bounce@'     . $fqdn, 'label' => 'Devoluções',       'icon' => 'fa-rotate-left','color' => 'bg-red-100 text-red-600'],
                        ];
                    @endphp
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach($emailUses as $eu)
                            <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 {{ $eu['color'] }} rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fa-solid {{ $eu['icon'] }} text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-gray-500">{{ $eu['label'] }}</p>
                                    <p class="text-xs text-gray-800 font-mono truncate mt-0.5">{{ $eu['address'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-3">
                        Os seus clientes podem responder directamente a estes endereços. As respostas aparecem automaticamente nos leads correspondentes.
                    </p>
                @else
                    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($mailDiagnostics as $item)
                            <div class="flex items-center gap-3 rounded-xl border px-4 py-3 {{ $item['ok'] ? 'border-emerald-100 bg-emerald-50' : 'border-red-100 bg-red-50' }}">
                                <span class="flex h-8 w-8 items-center justify-center rounded-lg {{ $item['ok'] ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    <i class="fa-solid {{ $item['ok'] ? 'fa-check' : 'fa-xmark' }} text-xs"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $item['label'] }}</p>
                                    <p class="text-xs {{ $item['ok'] ? 'text-emerald-700' : 'text-red-700' }}">{{ $item['ok'] ? 'Configurado' : 'Em falta' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(!$isReady)
                    <div class="mt-5 flex flex-col gap-3 sm:flex-row">
                        <button wire:click="refreshMailStatus" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50">
                            <i class="fa-solid fa-rotate text-xs"></i>
                            Atualizar estado
                        </button>
                        <button wire:click="reprovisionMail" wire:loading.attr="disabled"
                                class="inline-flex items-center justify-center gap-2 rounded-xl bg-[#2c6fad] px-4 py-2 text-sm font-medium text-white transition-colors hover:bg-[#1f5fa3] disabled:opacity-50">
                            <span wire:loading.remove wire:target="reprovisionMail">
                                <i class="fa-solid {{ $isFailed || $hasStalled ? 'fa-rotate-right' : 'fa-envelope' }} mr-1.5"></i>
                                {{ $isFailed || $hasStalled ? 'Tentar novamente' : 'Activar email' }}
                            </span>
                            <span wire:loading wire:target="reprovisionMail">A activar...</span>
                        </button>
                    </div>
                @endif
                </div>
            </div>

        </div>
    @endif

    {{-- ── TEAMS ───────────────────────────────────────────── --}}
    @if($tab === 'teams')
        <div class="space-y-4">

            @if(session('success_teams'))
                <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
                    <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    {{ session('success_teams') }}
                </div>
            @endif

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="font-semibold text-gray-900">Equipas</h2>
                    <p class="text-sm text-gray-500">Cada equipa tem o seu alias de email para receber e enviar mensagens.</p>
                </div>
                <button wire:click="openCreateTeam"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nova Equipa
                </button>
            </div>

            {{-- Teams list --}}
            @forelse($teams as $team)
                @php
                    $emailAddress = $team->emailAddress();
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-start justify-between">
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-3 mb-2">
                                <div class="w-9 h-9 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $team->name }}</h3>
                                    @if($team->description)
                                        <p class="text-xs text-gray-500">{{ $team->description }}</p>
                                    @endif
                                </div>
                            </div>

                            @if($emailAddress)
                                <div class="flex items-center gap-2 mb-3">
                                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    <code class="text-xs text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded">{{ $emailAddress }}</code>
                                </div>
                            @endif

                            <div class="flex flex-wrap gap-1.5">
                                @forelse($team->members as $member)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 text-gray-700 text-xs rounded-full">
                                        <span class="w-4 h-4 rounded-full bg-indigo-400 text-white text-xs flex items-center justify-center font-medium">
                                            {{ strtoupper(substr($member->name, 0, 1)) }}
                                        </span>
                                        {{ $member->name }}
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400 italic">Sem membros</span>
                                @endforelse
                            </div>
                        </div>

                        <div class="flex gap-2 ml-4 shrink-0">
                            <button wire:click="openEditTeam({{ $team->id }})"
                                    class="p-2 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </button>
                            <button wire:click="confirmDeleteTeam({{ $team->id }})"
                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-dashed border-gray-300 p-10 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <p class="text-sm font-medium text-gray-700">Nenhuma equipa criada</p>
                    <p class="text-xs text-gray-400 mt-1">Crie equipas para organizar os seus leads por departamento.</p>
                </div>
            @endforelse
        </div>

        {{-- Team form modal --}}
        @if($showTeamForm)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
                 wire:click.self="$set('showTeamForm', false)">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">{{ $editingTeamId ? 'Editar Equipa' : 'Nova Equipa' }}</h3>
                        <button wire:click="$set('showTeamForm', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nome da equipa *</label>
                            <input type="text" wire:model="teamName" placeholder="Ex: Equipa Comercial"
                                   class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('teamName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Alias de email
                                <span class="text-gray-400 text-xs font-normal ml-1">(ex: vendas → vendas@empresa.khuma.store)</span>
                            </label>
                            <div class="flex items-center gap-0 rounded-xl border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                                <input type="text" wire:model="teamEmailAlias"
                                       placeholder="vendas"
                                       class="flex-1 px-3 py-2 text-sm border-0 focus:ring-0 focus:outline-none">
                                @php $fqdn = auth()->user()->company?->mail_subdomain ? '@'.auth()->user()->company->mail_subdomain.'.'.config('services.mail_tenant.parent_domain') : '@empresa.dominio.com'; @endphp
                                <span class="px-3 py-2 bg-gray-50 text-xs text-gray-500 border-l border-gray-300 shrink-0">{{ $fqdn }}</span>
                            </div>
                            @error('teamEmailAlias') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Descrição</label>
                            <input type="text" wire:model="teamDescription" placeholder="Responsável por..."
                                   class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Membros</label>
                            <div class="max-h-40 overflow-y-auto border border-gray-200 rounded-xl divide-y divide-gray-100">
                                @forelse($availableUsers as $u)
                                    <label class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 cursor-pointer">
                                        <input type="checkbox" wire:model="teamMembers" value="{{ $u['id'] }}"
                                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <div>
                                            <p class="text-sm font-medium text-gray-800">{{ $u['name'] }}</p>
                                            <p class="text-xs text-gray-400">{{ $u['email'] }}</p>
                                        </div>
                                    </label>
                                @empty
                                    <p class="px-4 py-3 text-sm text-gray-400">Nenhum utilizador disponível.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-100">
                        <button wire:click="$set('showTeamForm', false)"
                                class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button wire:click="saveTeam"
                                class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-xl hover:bg-indigo-700 transition-colors">
                            Guardar
                        </button>
                    </div>
                </div>
            </div>
        @endif

        {{-- Delete confirm --}}
        @if($showDeleteConfirm)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Eliminar equipa?</h3>
                    <p class="text-sm text-gray-500 mb-5">Esta acção não pode ser desfeita.</p>
                    <div class="flex gap-3 justify-center">
                        <button wire:click="$set('showDeleteConfirm', false)"
                                class="px-4 py-2 text-sm border border-gray-300 rounded-xl hover:bg-gray-50">Cancelar</button>
                        <button wire:click="deleteTeam"
                                class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-xl hover:bg-red-700">Eliminar</button>
                    </div>
                </div>
            </div>
        @endif
    @endif

</div>
