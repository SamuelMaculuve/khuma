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

            {{-- Status card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-start justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">Estado do Servidor de Email</h2>
                        <p class="text-sm text-gray-500 mt-1">Cada empresa tem o seu subdomínio de email dedicado.</p>
                    </div>
                    @php
                        $statusMap = [
                            'ready'        => ['bg-emerald-100 text-emerald-800', 'Activo'],
                            'provisioning' => ['bg-amber-100 text-amber-800', 'A configurar...'],
                            'failed'       => ['bg-red-100 text-red-800', 'Falhou'],
                            'pending'      => ['bg-gray-100 text-gray-700', 'Pendente'],
                        ];
                        [$badgeClass, $badgeLabel] = $statusMap[$mailStatus] ?? $statusMap['pending'];
                    @endphp
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $badgeClass }}">
                        {{ $badgeLabel }}
                    </span>
                </div>

                @if($company?->mail_subdomain)
                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @php
                            $parent  = config('services.mail_tenant.parent_domain');
                            $fqdn    = $company->mail_subdomain . '.' . $parent;
                            $aliases = ['catchall' => '@', 'bounce' => 'bounce@', 'commercial' => 'commercial@', 'campaign' => 'campaign@'];
                        @endphp
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Subdomínio</p>
                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 rounded-lg">
                                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                <code class="text-sm font-mono text-gray-800">{{ $fqdn }}</code>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 mb-2">Aliases activos</p>
                            <div class="space-y-1">
                                @foreach($aliases as $name => $prefix)
                                    <div class="flex items-center gap-2 text-xs text-gray-600">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-400 shrink-0"></div>
                                        <code>{{ $prefix }}{{ $fqdn }}</code>
                                        <span class="text-gray-400">({{ $name }})</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($company?->mail_provision_error)
                    <div class="mt-4 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-700">
                        <strong>Erro:</strong> {{ $company->mail_provision_error }}
                    </div>
                @endif

                @if(session('success_mail'))
                    <div class="mt-4 px-4 py-3 bg-emerald-50 border border-emerald-200 rounded-xl text-sm text-emerald-700">
                        {{ session('success_mail') }}
                    </div>
                @endif

                <div class="mt-5 flex gap-3">
                    <button wire:click="reprovisionMail" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-xl hover:bg-indigo-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="reprovisionMail">
                            {{ $mailStatus === 'ready' ? 'Reprovisionar' : 'Configurar email' }}
                        </span>
                        <span wire:loading wire:target="reprovisionMail">A iniciar...</span>
                    </button>
                </div>
            </div>

            {{-- Mailcow connection test --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h2 class="font-semibold text-gray-900 mb-1">Ligação ao Mailcow</h2>
                <p class="text-sm text-gray-500 mb-4">Verifique a ligação ao servidor Mailcow da sua organização.</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">URL do Mailcow</label>
                        <input type="url" wire:model="mailcowUrl"
                               placeholder="https://mail.seudominio.com"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">API Key</label>
                        <input type="password" wire:model="mailcowKey"
                               placeholder="••••••••••••"
                               class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                @if($mailTestResult)
                    @php [$type, $msg] = explode(':', $mailTestResult, 2); @endphp
                    <div class="mb-4 px-4 py-3 rounded-xl text-sm
                         {{ $type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-700' : 'bg-red-50 border border-red-200 text-red-700' }}">
                        {{ $msg }}
                    </div>
                @endif

                <button wire:click="testMailConnection" wire:loading.attr="disabled"
                        class="px-4 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-xl hover:bg-gray-50 transition-colors disabled:opacity-50">
                    <span wire:loading.remove wire:target="testMailConnection">Testar ligação</span>
                    <span wire:loading wire:target="testMailConnection">A testar...</span>
                </button>
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
