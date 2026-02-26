<div>
    <!-- Cabeçalho -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Conexão WhatsApp </h1>
        <p class="text-gray-600">Gerencie a conexão da sua instância do WhatsApp via Khuma</p>
    </div>
    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="font-medium text-start text-blue-800">Como fazer teste:</h3>
                <ul class="text-sm text-start text-blue-700 mt-1 space-y-1">
                    <li>• Clique em <strong>Criar instância</strong></li>
                    <li>• Adicione o numero vinculado ao whatsapp o campo abaixo</li>
                    <li>• Clique em <strong>Conectar Whatsapp</strong></li>
                    <li>• A instância de teste tem duracao de <strong>1 hora</strong></li>
                    <li>• Desconecte o whatsapp para poder apagar a instancia</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="max-w-4xl mx-auto p-6 bg-white rounded-xl shadow-lg">

        <form action="{{ route('instance.store') }}" method="POST"
            class="flex gap-3 items-end {{ $currentInstance || $isEvolution ? 'hidden' : '' }}">
            @csrf

            <button type="submit"
                class="px-5 py-3 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
                Criar instância
            </button>
        </form>

        <div class="{{ !$isEvolution ? 'hidden' : '' }}">
            <!-- Status Atual -->
            <div class="mb-8 bg-gray-50 rounded-lg p-6 border border-gray-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-800">Status da Instância</h2>
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div
                                class="w-3 h-3 rounded-full mr-2 {{ $connected == 'connected' || $connected == 'open' ? 'bg-green-500' : 'bg-red-500' }}">
                            </div>
                            <span
                                class="text-sm font-medium {{ $connected == 'connected' || $connected == 'open' ? 'text-green-700' : 'text-red-700' }}">
                                {{ $connected == 'connected' || $connected == 'open' ? 'Conectado' : 'Desconectado' }}
                            </span>
                        </div>

                        <!-- Indicador de Loading do Polling -->
                        @if ($connected != 'connected' && $connected != 'close' && $connected != 'open')
                            <div class="flex items-center text-blue-600">
                                <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span class="text-sm">Verificando...</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Informações da Instância -->
                @if ($currentInstance)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600">ID:</span>
                                <span class="font-medium">{{ $currentInstance->id ?? 'N/A' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Nome:</span>
                                <span class="font-medium">{{ $currentInstance->name ?? 'N/A' }}</span>
                            </div>
                            {{-- <div class="flex justify-between">
                                <span class="text-gray-600">Perfil:</span>
                                <span class="font-medium">{{ $instanceData['profileName'] ?? 'N/A' }}</span>
                            </div> --}}
                        </div>
                        <div class="space-y-2">
                            {{-- <div class="flex justify-between">
                                <span class="text-gray-600">Plataforma:</span>
                                <span class="font-medium">{{ $instanceData['plataform'] ?? 'N/A' }}</span>
                            </div> --}}
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span
                                    class="px-2 py-1 rounded text-xs font-medium
                            {{ ($currentInstance->status ?? '') == 'connected' || $currentInstance->status == 'open' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $currentInstance->status ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Última atualização:</span>
                                <span class="font-medium">
                                    @if (isset($currentInstance->updated_at))
                                        {{ \Carbon\Carbon::parse($currentInstance->updated_at)->format('d/m/Y H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
                <button wire:click="deleteInstance"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 flex items-center text-sm
            {{ !$currentInstance ? 'hidden' : '' }} "
                    {{ $connected != 'connected' && $connected != 'open' ? 'disabled' : '' }}>
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Apagar instancia agora
                </button>
            </div>

            <!-- QR Code -->
            @if ($connected == 'connecting' || $connected == 'close')
                <div class="mb-8 bg-white rounded-lg p-6 border border-gray-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Escaneie o QR Code</h3>
                        <button wire:click="refreshQrCode" wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 flex items-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Atualizar QR
                        </button>
                    </div>
                    <div class="flex flex-col items-center">
                        <div class="bg-white p-4 rounded-lg border-2 border-dashed border-blue-300 mb-4">
                            <img src="{{ $qrcode }}" alt="QR Code WhatsApp" class="w-64 h-64">
                        </div>
                        <p class="text-gray-600 text-center mb-4">
                            Abra o WhatsApp no seu celular, toque em <strong>Menu</strong> → <strong>WhatsApp
                                Web</strong> e escaneie este código
                        </p>
                        <div class="flex items-center text-blue-600">
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span>Aguardando conexão...</span>
                        </div>
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">O QR code será verificado automaticamente a cada 20
                                segundos</p>
                            <p class="text-sm text-gray-500 mt-1">Se o QR code expirar, clique em "Atualizar QR"</p>
                        </div>
                    </div>
                </div>
            @endif
            <div
                class="mb-3 flex flex-col sm:flex-row gap-4 justify-center {{ $connected != 'connected' && $connected != 'connecting' && $connected != 'close' && $connected != 'open' ? '' : 'hidden' }}">
                <x-phone-input prefixModel="country_code" numberModel="phone" />
                @error('phone')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <x-prompt-input myclass="{{ $connected != 'connected' && $connected != 'open' ? 'hidden' : '' }}"
                :model="$currentInstance" field="prompt"  />

            <!-- Botões de Ação -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center mb-8">
                <!-- Botão Conectar -->
                @if ($connected != 'connected' && $connected != 'connecting' && $connected != 'close' && $connected != 'open')
                    <button wire:click="connect" wire:loading.attr="disabled" wire:target="connect"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                        @if ($loading)
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Conectando...
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                            Conectar WhatsApp
                        @endif
                    </button>
                @endif

                @if ($connected == 'connecting')
                    <button wire:click="newqrcode" wire:loading.attr="disabled" wire:target="newqrcode"
                        class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                        @if ($loading)
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Carregando...
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4">
                                </path>
                            </svg>
                            Novo Qr Code
                        @endif
                    </button>
                @endif

                <!-- Botão Desconectar -->
                @if ($connected == 'connected')
                    <button wire:click="disconnect" wire:loading.attr="disabled" wire:target="disconnect"
                        class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                        @if ($loading)
                            <svg class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Desconectando...
                        @else
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Desconectar WhatsApp
                        @endif
                    </button>
                @endif

                <!-- Botão Verificar Status -->
                <button wire:click="checkStatus"
                    class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Verificar Status
                </button>

            </div>
            <!-- Mensagem de Erro -->
            @if ($error)
                <div class="mb-8 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-red-700">{{ $error }}</span>
                    </div>
                </div>
            @endif
        </div>




        <!-- Script para controlar o polling -->
        <script>
            document.addEventListener('livewire:init', () => {
                let connectionTimeout;

                // Quando uma nova conexão é iniciada, aguarda 30 segundos antes de começar a verificar
                Livewire.on('connection-started', (data) => {
                    console.log('Nova conexão iniciada, ID:', data.connectionId);

                    // Limpa qualquer timeout anterior
                    if (connectionTimeout) {
                        clearTimeout(connectionTimeout);
                    }

                    // Aguarda 30 segundos antes de permitir verificações automáticas
                    // Isso dá tempo para o usuário escanear o QR code
                    connectionTimeout = setTimeout(() => {
                        console.log('Agora verificações automáticas estão ativas');
                    }, 30000); // 30 segundos
                });

                // Limpa o timeout quando o componente é desmontado
                Livewire.hook('component.unmount', () => {
                    if (connectionTimeout) {
                        clearTimeout(connectionTimeout);
                    }
                });
            });
        </script>

        <!-- Estilos adicionais -->
        <style>
            .fade-in {
                animation: fadeIn 0.5s ease-in-out;
            }

            @keyframes fadeIn {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            pre {
                font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
                font-size: 12px;
                line-height: 1.4;
            }

            /* Estilo para o QR code */
            img[alt="QR Code WhatsApp"] {
                image-rendering: crisp-edges;
                image-rendering: -webkit-optimize-contrast;
            }
        </style>
    </div>

    <!-- Polling condicional apenas quando necessário -->
    @if (!$connected || !$loggedIn)
        <div wire:poll.2m="checkStatus"></div>
    @endif

</div>
