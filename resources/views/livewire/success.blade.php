<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Subscrição Activada') }}
        </h2>
    </x-slot>

    <div class="py-20">
        <div class="max-w-md mx-auto text-center px-6">
            <div class="text-6xl mb-6">🎉</div>
            <h1 class="text-3xl font-bold text-gray-800 mb-3">Pagamento Confirmado!</h1>
            <p class="text-gray-500 mb-8">
                A sua subscrição está activa. Pode agora usar todos os recursos do seu plano.
            </p>
            <a href="{{ route('dashboard') }}"
               class="inline-block bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-xl font-semibold transition">
                Ir para o Dashboard
            </a>
        </div>
    </div>
</x-app-layout>
