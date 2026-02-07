<div>
    <x-app-layout>
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Escolha o seu plano e clique em continuar abaixo') }}
            </h2>

            <div class="max-w-6xl mx-auto py-10">


                <div class="grid md:grid-cols-3 gap-6">
                    @foreach ($plans as $plan)
                        {{-- <div class="border rounded-xl p-6 shadow-sm hover:shadow-lg transition"
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
                        </div> --}}
                        @if ($plan->code == 'ubuntu')
                            <div class="pricing-card bg-white p-8 rounded-2xl shadow-soft flex flex-col h-full"
                                :class="{ 'popular': {{ $selectedPlanId == $plan->id ? 'true' : 'false' }} }">
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-r from-gray-100 to-gray-50 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-seedling text-gray-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                            <p class="text-sm text-gray-500">Pequenos negócios</p>
                                        </div>
                                    </div>
                                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($plan->currentPrice()->amount, 2) }}<span
                                            class="text-sm font-normal text-gray-500"> MZN/mês + IVA</span></div>
                                    <span class="text-sm font-normal text-red-500"> Antes <del>3.000</del>
                                        <small>MZN/mês</small></span>
                                    <p class="text-gray-600">Ideal para quem está começando com automação no WhatsApp.
                                    </p>
                                </div>

                                <ul class="mb-8 space-y-4 flex-grow">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>2 Membros da equipa</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>500 linhas no fluxo do chatbot</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>1 instância WhatsApp (QR Code)</span>
                                    </li>
                                    <li class="flex items-start text-gray-400">
                                        <i class="fas fa-times mt-1 mr-3"></i>
                                        <span>Venda de produtos/serviços</span>
                                    </li>
                                    <li class="flex items-start text-gray-400">
                                        <i class="fas fa-times mt-1 mr-3"></i>
                                        <span>Mensagens em massa</span>
                                    </li>
                                </ul>

                                <button wire:click="selectPlan({{ $plan->id }})"
                                    class="bg-gradient-to-r from-primary to-secondary text-white text-center py-4 rounded-xl font-semibold hover:opacity-90 transition shadow-md">Escolher
                                    UBUNTU</button>
                            </div>
                        @endif
                        @if ($plan->code == 'baoba')
                            <div class="pricing-card bg-white p-8 rounded-2xl shadow-card flex flex-col h-full"
                                :class="{ 'popular': {{ $selectedPlanId == $plan->id ? 'true' : 'false' }} }">
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-r from-green-100 to-green-50 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-tree text-primary text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                            <p class="text-sm text-gray-500">Empresas em crescimento</p>
                                        </div>
                                    </div>
                                    <div class="text-4xl font-bold text-gray-900 mb-2">{{ number_format($plan->currentPrice()->amount, 2) }}<span
                                            class="text-sm font-normal text-gray-500"> MZN/mês + IVA</span></div>
                                    <p class="text-gray-600">Perfeito para empresas que querem escalar vendas pelo
                                        WhatsApp.
                                    </p>
                                </div>

                                <ul class="mb-8 space-y-4 flex-grow">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span><strong>Tudo do UBUNTU +</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>5 Membros da equipa</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>1.500 linhas no fluxo do chatbot</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Venda de produtos/serviços ✅</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Múltiplos métodos de pagamento</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Integração Google Sheets</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>2 instâncias WhatsApp</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Mensagens em massa</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Suporte básico</span>
                                    </li>
                                </ul>

                                <button wire:click="selectPlan({{ $plan->id }})"
                                    class="bg-gradient-to-r from-primary to-secondary text-white text-center py-4 rounded-xl font-semibold hover:opacity-90 transition shadow-md">Escolher
                                    BAOBÁ</button>
                            </div>
                        @endif
                        @if ($plan->code == 'leao')
                            <div class="pricing-card bg-white p-8 rounded-2xl shadow-soft flex flex-col h-full">
                                <div class="mb-8">
                                    <div class="flex items-center mb-4">
                                        <div
                                            class="w-12 h-12 bg-gradient-to-r from-yellow-100 to-yellow-50 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-crown text-yellow-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <h3 class="text-2xl font-bold text-gray-900">{{ $plan->name }}</h3>
                                            <p class="text-sm text-gray-500">Empresas estabelecidas</p>
                                        </div>
                                    </div>
                                    <div class="text-4xl font-bold text-gray-900 mb-2">Personalizado</div>
                                    <p class="text-gray-600">Solução enterprise com recursos avançados e suporte
                                        prioritário.</p>
                                </div>

                                <ul class="mb-8 space-y-4 flex-grow">
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span><strong>Tudo do BAOBÁ +</strong></span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Membros ilimitados</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Fluxo do chatbot ilimitado</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Instâncias WhatsApp customizáveis</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>WhatsApp Templates API</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Formulários WhatsApp</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Suporte prioritário</span>
                                    </li>
                                    <li class="flex items-start">
                                        <i class="fas fa-check text-green-500 mt-1 mr-3"></i>
                                        <span>Cloud API & QR Code</span>
                                    </li>
                                </ul>

                                <a href="#" target="_blank"
                                    class="bg-gray-900 text-white text-center py-4 rounded-xl font-semibold hover:bg-gray-800 transition">Falar
                                    com Especialista</a>
                            </div>
                        @endif
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
