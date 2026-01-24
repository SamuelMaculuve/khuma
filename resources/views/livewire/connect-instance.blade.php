<div>

    <div class="py-6" x-data>
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h1 class="text-xl font-bold mb-4 text-center">
                    Uazapi – Gerenciamento de Instância WhatsApp
                </h1>

                <!-- Mensagens de Status -->
                @if($success)
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg" x-data="{ show: true }" x-show="show">
                        <div class="flex justify-between items-start">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-green-800">{{ $success }}</p>
                                </div>
                            </div>
                            <button @click="show = false" class="text-green-600 hover:text-green-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                @if($error)
                    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg" x-data="{ show: true }" x-show="show">
                        <div class="flex justify-between items-start">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Erro</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <p>{{ $error }}</p>
                                    </div>
                                </div>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-800">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Formulário para conectar -->
                <div class="mb-6 p-4 border border-gray-200 rounded-lg">
                    <h2 class="text-lg font-semibold mb-3">Configuração de Conexão</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                                Número de Telefone
                            </label>
                            <input
                                type="text"
                                id="phone"
                                wire:model="phone"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="258848293580"
                            >
                            @error('phone')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="token" class="block text-sm font-medium text-gray-700 mb-1">
                                Token de Autenticação
                            </label>
                            <input
                                type="text"
                                id="token"
                                wire:model="token"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="d6ea651f-edeb-4660-88fc-16735e4d4475"
                            >
                            @error('token')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-center space-x-3">
                        <button
                            wire:click="connect"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 transition-colors"
                        >
                        <span wire:loading.remove wire:target="connect">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            Conectar Instância
                        </span>
                            <span wire:loading wire:target="connect">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Conectando...
                        </span>
                        </button>

                        <button
                            wire:click="disconnect"
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-50 cursor-not-allowed"
                            class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors"
                        >
                        <span wire:loading.remove wire:target="disconnect">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Desconectar Instância
                        </span>
                            <span wire:loading wire:target="disconnect">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Desconectando...
                        </span>
                        </button>
                    </div>
                </div>

                <!-- Resultado da Conexão -->
                @if($instanceData && !isset($instanceData['error']))
                    <div class="mt-6">
                        <h2 class="text-lg font-semibold mb-4 text-center">
                            Estado da Instância
                        </h2>

                        <!-- STATUS -->
                        <div class="mb-6 p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">Nome:</strong>
                                        <span class="ml-2">{{ $instanceData['instance']['name'] ?? 'N/A' }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">Status:</strong>
                                        <span class="ml-2 px-3 py-1 rounded-full text-white text-sm font-medium
                                        {{ ($instanceData['connected'] ?? false) ? 'bg-green-500' : 'bg-yellow-500' }}">
                                        {{ $instanceData['response'] ?? 'Desconhecido' }}
                                    </span>
                                    </p>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">Conectado:</strong>
                                        <span class="ml-2">
                                        @if($instanceData['connected'] ?? false)
                                                <span class="text-green-600 font-semibold">Sim</span>
                                            @else
                                                <span class="text-red-600 font-semibold">Não</span>
                                            @endif
                                    </span>
                                    </p>
                                </div>

                                <div>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">Última desconexão:</strong>
                                        <span class="ml-2">{{ $instanceData['instance']['lastDisconnect'] ?? 'N/A' }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">Motivo:</strong>
                                        <span class="ml-2">{{ $instanceData['instance']['lastDisconnectReason'] ?? 'N/A' }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong class="text-gray-700">ID da Instância:</strong>
                                        <span class="ml-2 text-sm font-mono text-gray-600">{{ $instanceData['instance']['id'] ?? 'N/A' }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- QR CODE -->
                        @if(!empty($instanceData['instance']['qrcode']) && !($instanceData['connected'] ?? false))
                            <div class="text-center mt-8 p-6 border border-gray-200 rounded-lg bg-white">
                                <p class="mb-3 font-semibold text-gray-700">Escaneie o QR Code para conectar</p>
                                <p class="text-sm text-gray-500 mb-4">Use o WhatsApp no seu telefone para escanear este código</p>

                                <div class="flex justify-center">
                                    <div class="p-4 border-2 border-gray-300 rounded-lg inline-block bg-white">
                                        <img
                                            src="{{ $instanceData['instance']['qrcode'] }}"
                                            alt="QR Code para conexão WhatsApp"
                                            class="w-64 h-64 mx-auto"
                                            onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=\'py-8 text-gray-500\'>QR Code não disponível</div>';"
                                        >
                                    </div>
                                </div>

                                <div class="mt-4 text-sm text-gray-600">
                                    <p class="mb-1">1. Abra o WhatsApp no seu telefone</p>
                                    <p class="mb-1">2. Toque em <strong>Menu</strong> → <strong>Dispositivos conectados</strong></p>
                                    <p class="mb-1">3. Toque em <strong>Conectar um dispositivo</strong></p>
                                    <p class="mb-1">4. Aponte a câmera para o código QR acima</p>
                                </div>
                            </div>
                        @elseif($instanceData['connected'] ?? false)
                            <div class="text-center mt-8 p-6 border border-green-200 rounded-lg bg-green-50">
                                <div class="flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <p class="text-lg font-semibold text-green-700">Instância Conectada!</p>
                                </div>
                                <p class="text-gray-600">A instância está atualmente conectada ao WhatsApp.</p>
                            </div>
                        @endif

                        <!-- Ações Adicionais -->
                        <div class="mt-6 flex justify-center space-x-4">
                            <button
                                wire:click="checkStatus"
                                wire:loading.attr="disabled"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors text-sm"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Verificar Status
                            </button>

                            <button
                                wire:click="clearMessages"
                                class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors text-sm"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Limpar Mensagens
                            </button>
                        </div>
                    </div>
                @elseif($instanceData && isset($instanceData['error']))
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.998-.833-2.73 0L4.686 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Atenção</h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>Resposta da API: {{ $instanceData['error'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informações da API -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-md font-semibold text-gray-700 mb-3">Informações da API Uazapi</h3>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-600 mb-1">Endpoints:</p>
                                <div class="space-y-1">
                                    <code class="text-xs bg-gray-100 p-1 rounded block">POST https://free.uazapi.com/instance/connect</code>
                                    <code class="text-xs bg-gray-100 p-1 rounded block">POST https://free.uazapi.com/instance/disconnect</code>
                                </div>
                            </div>
                            <div>
                                <p class="font-medium text-gray-600 mb-1">Headers:</p>
                                <div class="space-y-1">
                                    <code class="text-xs bg-gray-100 p-1 rounded block">Accept: application/json</code>
                                    <code class="text-xs bg-gray-100 p-1 rounded block">Content-Type: application/json</code>
                                    <code class="text-xs bg-gray-100 p-1 rounded block">token: {{ substr($token, 0, 8) }}...</code>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-xs text-gray-500">
                            <p class="font-medium mb-1">Nota:</p>
                            <p>Certifique-se de que o número de telefone está no formato internacional (ex: 258848293580 para Moçambique)</p>
                        </div>
                    </div>
                </div>

                <!-- Logs e Status -->
                <div class="mt-6 text-center">
                    <div class="inline-flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Última atualização: {{ now()->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estilos adicionais -->
    <style>
        .animate-spin {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- Scripts para interatividade -->
    <script>
        // Auto-atualização do status a cada 30 segundos se estiver conectando
        document.addEventListener('livewire:init', () => {
            setInterval(() => {
                if (@this.instanceData && @this.instanceData.connected === false) {
                    Livewire.dispatch('check-status');
                }
            }, 30000); // 30 segundos
        });
    </script>
</div>
