<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Escolha o seu plano') }}
            </h2>
            <div class="max-w-md mx-auto bg-white p-6 rounded-xl shadow">
                <h2 class="text-xl font-bold mb-4">Confirmar Subscrição</h2>

                <p class="mb-2 text-gray-600">
                    Metodo de Pagamento: <strong>M-Pesa</strong>
                </p>

                <p class="mb-2 text-gray-600">
                    Plano: <strong>{{ $plan->name }}</strong>
                </p>

                <p class="mb-4">
                    Valor: <strong>{{ number_format($plan->currentPrice()->amount, 2) }} MT</strong>
                </p>

                <input type="tel" wire:model="phone" placeholder="Número M-Pesa"
                    class="w-full border rounded px-3 py-2 mb-3" maxlength="9">

                @error('phone')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror

                <button wire:click="pay" wire:loading.attr="disabled"
                    class="w-full bg-green-600 text-white py-2 rounded hover:bg-green-700">
                    Pagar com M-Pesa
                </button>
            </div>
        </x-slot>
    </x-app-layout>
</div>
