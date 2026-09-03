<div class="min-h-[calc(100vh-44px)] bg-slate-50">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-[#2c6fad]">Admin plataforma</p>
                <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Gestão de planos</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Configure preços, limites e funcionalidades globais para os clientes que subscrevem o SaaS.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-5 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid gap-5 lg:grid-cols-[320px_1fr]">
            <aside class="space-y-3">
                @foreach($plans as $plan)
                    @php $price = $plan->currentPrice(); @endphp
                    <button wire:click="selectPlan({{ $plan->id }})"
                            class="w-full rounded-lg border bg-white p-4 text-left shadow-sm transition hover:border-[#2c6fad] {{ $selectedPlanId === $plan->id ? 'border-[#2c6fad] ring-4 ring-blue-100' : 'border-slate-200' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-bold text-slate-950">{{ $plan->name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $plan->code }}</p>
                            </div>
                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-600">
                                {{ $price ? number_format($price->amount, 0) . ' ' . $price->currency : '-' }}
                            </span>
                        </div>
                    </button>
                @endforeach
            </aside>

            <section class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm">
                <form wire:submit="save" class="space-y-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="text-sm font-semibold text-slate-700">Nome do plano</label>
                            <input type="text" wire:model="name" class="mt-1 w-full rounded-lg border-slate-300 focus:border-[#2c6fad] focus:ring-[#2c6fad]">
                            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div class="grid grid-cols-[1fr_100px] gap-3">
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Preço mensal</label>
                                <input type="number" step="0.01" wire:model="amount" class="mt-1 w-full rounded-lg border-slate-300 focus:border-[#2c6fad] focus:ring-[#2c6fad]">
                                @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="text-sm font-semibold text-slate-700">Moeda</label>
                                <input type="text" wire:model="currency" maxlength="3" class="mt-1 w-full rounded-lg border-slate-300 uppercase focus:border-[#2c6fad] focus:ring-[#2c6fad]">
                                @error('currency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="text-sm font-semibold text-slate-700">Descrição</label>
                        <textarea wire:model="description" rows="3" class="mt-1 w-full rounded-lg border-slate-300 focus:border-[#2c6fad] focus:ring-[#2c6fad]"></textarea>
                        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <h2 class="text-base font-bold text-slate-950">Funcionalidades e limites</h2>
                        <div class="mt-4 grid gap-3 md:grid-cols-2">
                            @foreach($featureCatalog as $key => $meta)
                                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                    <label class="flex items-start gap-3">
                                        <input type="checkbox" wire:model="features.{{ $key }}.enabled" class="mt-1 rounded border-slate-300 text-[#2c6fad] focus:ring-[#2c6fad]">
                                        <span class="flex-1">
                                            <span class="block text-sm font-bold text-slate-800">{{ $meta['label'] }}</span>
                                            <span class="block text-xs text-slate-500">{{ $key }}</span>
                                        </span>
                                    </label>

                                    @if($meta['type'] === 'limit')
                                        <input type="text" wire:model="features.{{ $key }}.value" placeholder="Ex: 5 ou unlimited" class="mt-3 w-full rounded-lg border-slate-300 text-sm focus:border-[#2c6fad] focus:ring-[#2c6fad]">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end border-t border-slate-100 pt-5">
                        <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-[#2c6fad] px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-[#245b8e]">
                            <i class="fa-solid fa-floppy-disk text-xs"></i>
                            Guardar plano
                        </button>
                    </div>
                </form>
            </section>
        </div>
    </div>
</div>
