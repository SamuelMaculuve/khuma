<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Escolha o seu plano') }}
        </h2>

        <div class="max-w-xl mx-auto py-12">
        <h1 class="text-2xl font-bold mb-6">Confirmar Subscrição  - 1</h1>


        <div class="border rounded-xl p-6 shadow">
            <h2 class="text-lg font-semibold">Plano {{ $plan->name }}</h2>


            <ul class="text-sm text-gray-600 mt-4 space-y-1">
                @foreach ($plan->features as $feature)
                    <li>• {{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}: {{ $feature->feature_value }}</li>
                @endforeach
            </ul>


            <div class="mt-6">
                <button wire:click="subscribe" class="w-full bg-indigo-600 text-white py-3 rounded-lg">
                    Efetuar Pagamento
                </button>
            </div>
        </div>
    </div>

    </x-slot>

</x-app-layout>
