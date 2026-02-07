<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Escolha o seu plano') }}
            </h2>

            <div class="max-w-6xl mx-auto py-10">
                <h1 class="text-3xl font-bold mb-8 text-center">Escolha o seu plano</h1>


                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ($plans as $plan)
                        <div class="border rounded-xl p-6 shadow-sm hover:shadow-lg transition"
                            :class="{ 'ring-2 ring-indigo-600': {{ $selectedPlanId == $plan->id ? 'true' : 'false' }} }">

                            <h2 class="text-xl font-semibold mb-2">{{ $plan->name }}</h2>

                            <ul class="text-sm text-gray-600 space-y-1 mb-4">
                                @foreach ($plan->features as $feature)
                                    <li>• {{ ucfirst(str_replace('_', ' ', $feature->feature_key)) }}:
                                        {{ $feature->feature_value }}</li>
                                @endforeach
                            </ul>

                            <button wire:click="selectPlan({{ $plan->id }})"
                                class="w-full py-2 rounded-lg border border-indigo-600 text-indigo-600 hover:bg-indigo-50">
                                Selecionar
                            </button>
                        </div>
                    @endforeach
                </div>


                @if ($selectedPlanId)
                    <div class="text-center mt-8">
                        <button wire:click="continue" class="px-6 py-3 bg-indigo-600 text-white rounded-xl">
                            Continuar para pagamento
                        </button>
                    </div>
                @endif
            </div>

        </x-slot>

    </x-app-layout>

</div>
