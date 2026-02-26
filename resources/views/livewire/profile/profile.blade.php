<div x-data="{
    tab: 'profile',
    toastProfile: false,
    toastPassword: false,
}"
     x-on:profile-saved.window="toastProfile = true; setTimeout(() => toastProfile = false, 3500)"
     x-on:password-saved.window="toastPassword = true; setTimeout(() => toastPassword = false, 3500)"
     class="min-h-screen bg-gray-50"
>

    {{-- ── Toasts ─────────────────────────────────────────────────────────── --}}
    <div class="fixed top-5 right-5 z-50 flex flex-col gap-2 pointer-events-none">
        <div x-show="toastProfile" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-6" x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-x-6"
             class="pointer-events-auto flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Perfil actualizado com sucesso!
        </div>
        <div x-show="toastPassword" x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-x-6" x-transition:enter-end="opacity-100 translate-x-0"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-x-6"
             class="pointer-events-auto flex items-center gap-3 bg-blue-600 text-white px-5 py-3 rounded-xl shadow-xl text-sm font-medium">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Palavra-passe alterada!
        </div>
    </div>

    {{-- ── Page header ──────────────────────────────────────────────────────── --}}
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center gap-5">
                {{-- Avatar --}}
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-600 flex items-center justify-center text-white text-2xl font-bold shadow-md select-none">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ auth()->user()->name }}</h1>
                    <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>

                @if ($subscription)
                    <div class="ml-auto">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                            {{ $subscription->isActive() ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                {{ $subscription->isActive() ? 'bg-emerald-500' : 'bg-red-500' }}"></span>
                            {{ $subscription->isActive() ? 'Activo' : 'Expirado' }}
                        </span>
                    </div>
                @endif
            </div>

            {{-- Tabs --}}
            <div class="flex gap-1 mt-6 border-b border-gray-200">
                @foreach ([
                    ['profile',      'Perfil',          'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
                    ['subscription', 'Subscrição',       'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['security',     'Segurança',        'M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z'],
                ] as [$key, $label, $icon])
                    <button
                        @click="tab = '{{ $key }}'"
                        :class="tab === '{{ $key }}'
                            ? 'border-b-2 border-emerald-600 text-emerald-700 font-semibold'
                            : 'text-gray-500 hover:text-gray-700'"
                        class="flex items-center gap-2 px-4 pb-3 text-sm transition-colors -mb-px"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" />
                        </svg>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">

        {{-- ══════════════════════════════════════════════════════
             TAB: PERFIL
        ══════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'profile'" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Informações Pessoais</h2>
                <div class="grid sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome completo</label>
                        <input wire:model="name" type="text"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                        <input wire:model="email" type="email"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button wire:click="updateProfile" wire:loading.attr="disabled" wire:target="updateProfile"
                            class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition">
                        <span wire:loading.remove wire:target="updateProfile">Guardar alterações</span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            A guardar...
                        </span>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════════════════════════════════════════════
             TAB: SUBSCRIÇÃO
        ══════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'subscription'" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">

            @if ($subscription)
                {{-- Card plano activo --}}
                <div class="bg-gradient-to-br from-emerald-600 to-teal-700 text-white rounded-2xl p-8 shadow-md mb-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-emerald-200 text-sm font-medium uppercase tracking-wider mb-1">Plano Actual</p>
                            <h2 class="text-3xl font-bold">{{ $subscription->plan->name }}</h2>
                            <p class="text-emerald-100 mt-1 text-sm">
                                {{ number_format($subscription->price, 2) }} MZN / mês
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur px-3 py-1.5 rounded-full text-xs font-semibold">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                                {{ $subscription->isActive() ? 'Activo' : 'Expirado' }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mt-8">
                        <div class="bg-white/15 rounded-xl p-4">
                            <p class="text-emerald-200 text-xs mb-1">Início</p>
                            <p class="font-semibold text-sm">{{ $subscription->start_date?->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-white/15 rounded-xl p-4">
                            <p class="text-emerald-200 text-xs mb-1">Renovação</p>
                            <p class="font-semibold text-sm">{{ $subscription->end_date?->format('d/m/Y') }}</p>
                        </div>
                        <div class="bg-white/15 rounded-xl p-4">
                            <p class="text-emerald-200 text-xs mb-1">Dias restantes</p>
                            <p class="font-semibold text-sm">{{ $subscription->daysLeft() }} dias</p>
                        </div>
                    </div>
                </div>

                {{-- Pagamentos recentes --}}
                @if ($payments->count())
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="font-bold text-gray-800">Pagamentos Recentes</h3>
                            <a href="{{ route('subscription.payments') }}" class="text-sm text-emerald-600 hover:underline font-medium">
                                Ver todos →
                            </a>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @foreach ($payments as $payment)
                                <div class="flex items-center justify-between py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl
                                            {{ $payment->status === 'paid' ? 'bg-emerald-50' : ($payment->status === 'pending' ? 'bg-amber-50' : 'bg-red-50') }}
                                            flex items-center justify-center">
                                            @if ($payment->status === 'paid')
                                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                            @elseif ($payment->status === 'pending')
                                                <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            @endif
                                        </div>
                                        <div>
{{--                                            <p class="text-sm font-medium text-gray-800">{{ $payment->subscription->plan->name }}</p>--}}
                                            <p class="text-xs text-gray-400">{{ $payment->created_at->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-semibold text-gray-800">{{ number_format($payment->amount, 2) }} MZN</p>
                                        <span class="text-xs
                                            {{ $payment->status === 'paid' ? 'text-emerald-600' : ($payment->status === 'pending' ? 'text-amber-500' : 'text-red-500') }}">
                                            {{ ucfirst($payment->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Mudar de plano --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-800 mb-1">Mudar de Plano</h3>
                    <p class="text-sm text-gray-500 mb-4">Escolha um plano diferente conforme as suas necessidades.</p>
                    <a href="{{ route('subscription.plans') }}"
                       class="inline-flex items-center gap-2 border border-emerald-600 text-emerald-700 hover:bg-emerald-50 px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Ver planos disponíveis
                    </a>
                </div>

            @else
                {{-- Sem subscrição --}}
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Sem subscrição activa</h3>
                    <p class="text-gray-500 text-sm mb-6">Subscreva um plano para desbloquear todos os recursos.</p>
                    <a href="{{ route('subscription.plans') }}"
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-sm">
                        Ver Planos
                    </a>
                </div>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════
             TAB: SEGURANÇA
        ══════════════════════════════════════════════════════ --}}
        <div x-show="tab === 'security'" x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h2 class="text-lg font-bold text-gray-800 mb-6">Alterar Palavra-passe</h2>
                <div class="max-w-md space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nova palavra-passe</label>
                        <input wire:model="password" type="password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirmar palavra-passe</label>
                        <input wire:model="password_confirmation" type="password"
                               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition" />
                    </div>
                    <div class="pt-2">
                        <button wire:click="updatePassword" wire:loading.attr="disabled" wire:target="updatePassword"
                                class="inline-flex items-center gap-2 bg-gray-900 hover:bg-gray-800 disabled:opacity-60 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition">
                            <span wire:loading.remove wire:target="updatePassword">Actualizar palavra-passe</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                                </svg>
                                A guardar...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
