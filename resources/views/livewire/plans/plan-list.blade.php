<div class="py-6">
    <div class="px-4 sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg pt-3">
            {{-- Flash messages --}}
            @if (session()->has('success'))
                <div
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="setTimeout(() => show = false, 4000)"
                    x-transition:leave="transition ease-in duration-300"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="mb-6 flex items-center gap-3 rounded-xl bg-emerald-50 border border-emerald-200 px-5 py-4 text-emerald-800 shadow-sm"
                >
                    <svg class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header --}}
            <div class="px-4 sm:px-6 lg:px-8 mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-5">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-gray-900">Planos</h1>
                    <p class="mt-1 text-sm text-gray-500">Gerencie os planos de subscrição e os respectivos preços e funcionalidades.</p>
                </div>
                <button
                    wire:click="openCreate"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Novo Plano
                </button>
            </div>

            <div class="px-4 sm:px-6 lg:px-8 pb-7">
                {{-- Table --}}
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Código</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Nome</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Descrição</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Preço Activo</th>
                            <th class="px-6 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Funcionalidades</th>
                            <th class="px-6 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Acções</th>
                        </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        @forelse ($plans as $plan)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-indigo-100">
                                {{ $plan->code }}
                            </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $plan->name }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ $plan->description ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if ($plan->activePrice)
                                        <span class="font-semibold">{{ number_format($plan->activePrice->amount, 2) }}</span>
                                        <span class="text-xs text-gray-400 ml-1">{{ $plan->activePrice->currency }}</span>
                                    @else
                                        <span class="text-gray-400 text-xs">Sem preço</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse ($plan->features as $feature)
                                            <span class="inline-flex items-center rounded-md bg-slate-100 px-2 py-0.5 text-xs text-slate-600">
                                        {{ $feature->feature_key }}:
                                        <span class="ml-1 font-semibold text-slate-800">{{ $feature->feature_value ?? '—' }}</span>
                                    </span>
                                        @empty
                                            <span class="text-xs text-gray-400">Nenhuma</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            wire:click="openEdit({{ $plan->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-colors"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Editar
                                        </button>
                                        <button
                                            wire:click="confirmDelete({{ $plan->id }})"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-red-100 bg-red-50 px-3 py-1.5 text-xs font-medium text-red-600 shadow-sm hover:bg-red-100 transition-colors"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3 text-gray-400">
                                        <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p class="text-sm font-medium">Nenhum plano encontrado.</p>
                                        <button wire:click="openCreate" class="text-sm font-semibold text-indigo-600 hover:text-indigo-500">Criar o primeiro plano →</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>

                    @if ($plans->hasPages())
                        <div class="border-t border-gray-100 px-6 py-4">
                            {{ $plans->links() }}
                        </div>
                    @endif
                </div>
            </div>

            {{-- ===================== CREATE / EDIT MODAL ===================== --}}
            @if ($showModal)
                <div
                    x-data
                    x-show="true"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4"
                >
                    <div
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        class="w-full max-w-2xl rounded-2xl bg-white shadow-2xl"
                    >
                        {{-- Modal Header --}}
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                            <h2 class="text-lg font-semibold text-gray-900">
                                {{ $planId ? 'Editar Plano' : 'Novo Plano' }}
                            </h2>
                            <button wire:click="closeModal" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition-colors">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="max-h-[70vh] overflow-y-auto px-6 py-6 space-y-6">

                            {{-- Basic info --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Código <span class="text-red-500">*</span></label>
                                    <input
                                        wire:model.live="code"
                                        type="text"
                                        placeholder="ex: ubuntu"
                                        class="block w-full rounded-xl border @error('code') border-red-400 bg-red-50 @else border-gray-200 @enderror px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition"
                                    />
                                    @error('code') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Nome <span class="text-red-500">*</span></label>
                                    <input
                                        wire:model.live="name"
                                        type="text"
                                        placeholder="ex: Ubuntu"
                                        class="block w-full rounded-xl border @error('name') border-red-400 bg-red-50 @else border-gray-200 @enderror px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition"
                                    />
                                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Descrição</label>
                                <textarea
                                    wire:model.live="description"
                                    rows="2"
                                    placeholder="Breve descrição do plano..."
                                    class="block w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition resize-none"
                                ></textarea>
                            </div>

                            {{-- Price --}}
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Preço <span class="text-red-500">*</span></label>
                                    <input
                                        wire:model.live="amount"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        placeholder="0.00"
                                        class="block w-full rounded-xl border @error('amount') border-red-400 bg-red-50 @else border-gray-200 @enderror px-3.5 py-2.5 text-sm text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition"
                                    />
                                    @error('amount') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Moeda</label>
                                    <select
                                        wire:model.live="currency"
                                        class="block w-full rounded-xl border border-gray-200 px-3.5 py-2.5 text-sm text-gray-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition bg-white"
                                    >
                                        <option value="MZN">MZN — Metical</option>
                                        <option value="USD">USD — Dólar</option>
                                        <option value="EUR">EUR — Euro</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Features --}}
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <label class="text-sm font-medium text-gray-700">Funcionalidades</label>
                                    <button
                                        type="button"
                                        wire:click="addFeature"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-500 transition-colors"
                                    >
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Adicionar
                                    </button>
                                </div>

                                @if (count($features) === 0)
                                    <p class="text-xs text-gray-400 italic">Nenhuma funcionalidade adicionada.</p>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($features as $i => $feature)
                                            <div class="flex items-start gap-2">
                                                <div class="flex-1 grid grid-cols-2 gap-2">
                                                    <div>
                                                        <input
                                                            wire:model.live="features.{{ $i }}.feature_key"
                                                            type="text"
                                                            placeholder="Chave (ex: members)"
                                                            class="block w-full rounded-lg border @error('features.'.$i.'.feature_key') border-red-400 @else border-gray-200 @enderror px-3 py-2 text-xs text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition"
                                                        />
                                                        @error('features.'.$i.'.feature_key')
                                                        <p class="mt-0.5 text-xs text-red-500">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                    <input
                                                        wire:model.live="features.{{ $i }}.feature_value"
                                                        type="text"
                                                        placeholder="Valor (ex: 5 ou unlimited)"
                                                        class="block w-full rounded-lg border border-gray-200 px-3 py-2 text-xs text-gray-900 placeholder-gray-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 outline-none transition"
                                                    />
                                                </div>
                                                <button
                                                    type="button"
                                                    wire:click="removeFeature({{ $i }})"
                                                    class="mt-0.5 rounded-lg p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-500 transition-colors"
                                                >
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center justify-end gap-3 border-t border-gray-100 px-6 py-4">
                            <button
                                wire:click="closeModal"
                                class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                wire:click="save"
                                wire:loading.attr="disabled"
                                wire:loading.class="opacity-70 cursor-not-allowed"
                                class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                            >
                        <span wire:loading.remove wire:target="save">
                            {{ $planId ? 'Actualizar' : 'Criar Plano' }}
                        </span>
                                <span wire:loading wire:target="save" class="flex items-center gap-2">
                            <svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            A guardar...
                        </span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif


            {{-- ===================== DELETE CONFIRMATION MODAL ===================== --}}
            @if ($showDeleteModal)
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 px-4">
                    <div
                        x-data
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        class="w-full max-w-sm rounded-2xl bg-white p-6 shadow-2xl"
                    >
                        <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-red-100">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1">Eliminar Plano</h3>
                        <p class="text-sm text-gray-500 mb-6">Esta acção é irreversível. O plano e todos os dados associados serão eliminados.</p>
                        <div class="flex gap-3">
                            <button
                                wire:click="closeModal"
                                class="flex-1 rounded-xl border border-gray-200 bg-white py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors"
                            >
                                Cancelar
                            </button>
                            <button
                                wire:click="delete"
                                wire:loading.attr="disabled"
                                class="flex-1 rounded-xl bg-red-600 py-2.5 text-sm font-semibold text-white hover:bg-red-500 transition-colors"
                            >
                                <span wire:loading.remove wire:target="delete">Eliminar</span>
                                <span wire:loading wire:target="delete">A eliminar...</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

    </div>

</div>
