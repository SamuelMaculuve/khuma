<div>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Confirmar Pagamento') }}
            </h2>
        </x-slot>

        {{-- Toast --}}
        @if ($errorMessage)
            <div
                x-data="{ show: true }"
                x-init="setTimeout(() => show = false, 5000)"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed top-6 right-6 z-50 flex items-start gap-3 bg-red-600 text-white px-5 py-4 rounded-xl shadow-lg max-w-sm"
            >
                <svg class="w-5 h-5 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8v4m0 4h.01M21 12A9 9 0 1 1 3 12a9 9 0 0 1 18 0z"/>
                </svg>
                <div>
                    <p class="font-semibold text-sm">Erro no pagamento</p>
                    <p class="text-xs opacity-90 mt-0.5">{{ $errorMessage }}</p>
                </div>
                <button @click="show = false" class="ml-auto opacity-70 hover:opacity-100">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        @endif

        <div class="py-12">
            <div class="max-w-md mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-2xl p-8">

                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Resumo da Subscrição</h2>

                    <div class="space-y-3 mb-6 text-sm">
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-500">Método de Pagamento</span>
                            <span class="font-semibold text-green-700">M-Pesa</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-500">Plano</span>
                            <span class="font-semibold">{{ $plan->name }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-500">Subtotal</span>
                            <span>{{ number_format($this->subtotal, 2) }} MZN</span>
                        </div>
                        <div class="flex justify-between py-2 border-b">
                            <span class="text-gray-500">IVA (16%)</span>
                            <span>{{ number_format($this->iva, 2) }} MZN</span>
                        </div>
                        <div class="flex justify-between py-2 text-base font-bold text-blue-700">
                            <span>Total</span>
                            <span>{{ number_format($this->total, 2) }} MZN</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Número M-Pesa</label>
                        <div class="flex items-center border rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-blue-500">
                            <span class="px-3 py-2 bg-gray-100 text-gray-600 text-sm border-r">+258</span>
                            <input
                                type="tel"
                                wire:model="phone"
                                placeholder="84 XXX XXXX"
                                maxlength="9"
                                class="flex-1 px-3 py-2 outline-none text-sm"
                            >
                        </div>
                        @error('phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        wire:click="pay"
                        wire:loading.attr="disabled"
                        wire:target="pay"
                        class="relative w-full bg-green-600 hover:bg-green-700 disabled:opacity-60 disabled:cursor-not-allowed text-white py-3 rounded-xl font-semibold transition"
                    >
                        {{-- Texto normal --}}
                        <span wire:loading.remove wire:target="pay" class="flex items-center justify-center gap-2">
                            💳 Pagar {{ number_format($this->total, 2) }} MZN
                        </span>

                        {{-- Loading --}}
                        <span wire:loading wire:target="pay" class="flex items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            A processar pagamento...
                        </span>
                    </button>

                    <p class="text-xs text-gray-400 text-center mt-4">
                        Receberá um pedido de confirmação no seu telemóvel M-Pesa.
                    </p>
                </div>
            </div>
        </div>
</div>
