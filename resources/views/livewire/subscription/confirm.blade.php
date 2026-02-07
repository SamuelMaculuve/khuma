<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Escolha o seu plano') }}
            </h2>
            <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-bold mb-4">Confirmar Subscrição</h2>

                <div class="flex flex-row w-full justify-between mb-4">
                    <span class="text-gray-600">Metodo de Pagamento:</span> <strong class="text-blue-700">M-Pesa</strong>
                </div>

                <div class="flex flex-row w-full justify-between mb-4">
                    <span class="text-gray-600">Plano:</span> <strong class="text-blue-700">{{ $plan->name }}</strong>
                </div>

                <div class="flex flex-row w-full justify-between mb-2">
                    <span class="text-gray-600">Preco:</span>
                    <strong class="text-blue-700">{{ number_format($plan->currentPrice()->amount, 2) }} MZN</strong>
                </div>
                <div class="flex flex-row w-full justify-between mb-2">
                    <span class="text-gray-600">IVA:</span>
                    <strong class="text-blue-700">{{ number_format($plan->currentPrice()->amount * 0.16, 2) }}
                        MZN</strong>
                </div>

                <div class="flex flex-row w-full justify-between mb-10">
                    <span class="text-gray-600">Total:</span>
                    <strong
                        class="text-blue-700">{{ number_format($plan->currentPrice()->amount + $plan->currentPrice()->amount * 0.16, 2) }}
                        MZN</strong>
                </div>

                <input type="tel" wire:model="phone" placeholder="Número M-Pesa"
                    class="w-full border rounded px-3 py-2 mb-3" maxlength="9">

                @error('phone')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror

                <button wire:click="pay" wire:loading.attr="disabled"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
                    Pagar com M-Pesa
                </button>
            </div>
        </x-slot>
    </x-app-layout>
</div>
