<div>
    <div class="flex h-screen bg-gray-50">
        <!-- Barra lateral esquerda (lista de tickets - simplificada) -->
        <!-- Conteúdo principal -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Cabeçalho -->
            <div class="bg-white border-b border-gray-200 p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">#{{ $ticket['id'] }} - {{ $ticket['title'] }}</h1>
                        <div class="flex items-center space-x-4 mt-2">
                            <div class="flex items-center space-x-2">
                                @foreach($statuses as $status)
                                    <button
                                        wire:click="changeStatus('{{ $status['id'] }}')"
                                        class="px-3 py-1 text-sm rounded-full border {{ $ticket['status'] === $status['id'] ? 'bg-' . $status['color'] . '-100 text-' . $status['color'] . '-800 border-' . $status['color'] . '-300' : 'bg-gray-100 text-gray-600 border-gray-300 hover:bg-gray-200' }}"
                                    >
                                        {{ $status['name'] }}
                                    </button>
                                @endforeach
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Estado atual:</span>
                                <span class="ml-1 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                @if($ticket['status'] === 'pending')
                                        Pendente
                                    @elseif($ticket['status'] === 'resolved')
                                        Resolvido
                                    @elseif($ticket['status'] === 'escalated')
                                        Escalado
                                    @else
                                        Aberto
                                    @endif
                            </span>
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-gray-500">Ticket ID: {{ $ticket['id'] }}</div>
                        <div class="text-sm text-gray-500 mt-1">{{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <div class="flex-1 flex-row">
                <div class="flex-1 overflow-auto">
                    <div class="grid grid-cols-3 gap-6 p-6">
                        <!-- Coluna da esquerda - Detalhes do ticket -->
                        <div class="col-span-2 space-y-6">
                            <!-- Tempo Máximo em Aberto -->
                            <div class="bg-white rounded-lg border border-gray-200 p-6">
                                <h2 class="text-lg font-semibold text-gray-800 mb-4">{{ $ticket['max_open_time'] }}</h2>

                                <div class="grid grid-cols-2 gap-4 mb-6">
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Equipa de Apoio ao Cliente</div>
                                        <div class="text-sm text-gray-800">{{ $ticket['team'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">HelpDesk IT</div>
                                        <div class="text-sm text-gray-800">{{ $ticket['helpdesk'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Atribuído a</div>
                                        <div class="text-sm text-gray-800 font-medium">{{ $ticket['assigned_to'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Prioridade</div>
                                        <div class="flex">
                                            @for($i = 1; $i <= 3; $i++)
                                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Tipo</div>
                                        <div class="text-sm text-gray-800">{{ $ticket['type'] }}</div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-500">Etiquetas</div>
                                        <div class="flex items-center space-x-2">
                                            @foreach($ticket['tags'] as $tag)
                                                <span class="px-2 py-1 bg-gray-100 text-gray-800 text-xs rounded flex items-center">
                                            {{ $tag }}
                                            <button class="ml-1 text-gray-400 hover:text-gray-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </span>
                                            @endforeach
                                            <button class="text-blue-600 text-sm hover:text-blue-800">+</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Subtickets</div>
                                    <div class="text-sm text-gray-800">
                                        <button wire:click="$toggle('showSubtickets')" class="text-blue-600 hover:text-blue-800">
                                            {{ $showSubtickets ? 'Ocultar' : 'Mostrar' }} subtickets
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-6">
                                    <div class="text-sm font-medium text-gray-500 mb-2">Motivo para escalar ou passar para pendente</div>
                                    <div class="text-sm text-gray-800 bg-gray-50 p-3 rounded border border-gray-200">
                                        {{ $ticket['reason'] }}
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Provider_Ticke:</span>
                                        <a href="{{ $ticket['provider_ticket'] }}" target="_blank" class="text-sm text-blue-600 hover:text-blue-800 ml-2">
                                            {{ $ticket['provider_ticket'] }}
                                        </a>
                                    </div>
                                    <div>
                                        <span class="text-sm font-medium text-gray-500">Path:</span>
                                        <span class="text-sm text-gray-800 ml-2">{{ $ticket['path'] }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Seção de informações do cliente -->
                            <div class="grid grid-cols-3 gap-6">
                                <!-- Tempo Máximo Em Resolvido -->
{{--                                <div class="bg-white rounded-lg border border-gray-200 p-6">--}}
{{--                                    <h3 class="text-md font-semibold text-gray-800 mb-4">{{ $ticket['max_resolved_time'] }}</h3>--}}
{{--                                    <div class="space-y-4">--}}
{{--                                        <div>--}}
{{--                                            <div class="text-sm font-medium text-gray-500">Cliente</div>--}}
{{--                                            <div class="text-sm text-gray-800">{{ $ticket['client'] }}</div>--}}
{{--                                        </div>--}}
{{--                                        <div>--}}
{{--                                            <div class="text-sm font-medium text-gray-500">Telefone</div>--}}
{{--                                            <div class="text-sm text-gray-800">{{ $ticket['phone'] }}</div>--}}
{{--                                        </div>--}}
{{--                                        <div>--}}
{{--                                            <div class="text-sm font-medium text-gray-500">Document</div>--}}
{{--                                            <div class="text-sm text-gray-800">-</div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}




                            </div>

                        </div>

                        <!-- Coluna da direita - Formulário para nova mensagem -->
                        <div class="space-y-6">
                            <div class="h-3/5 bg-white rounded-lg border border-gray-200 p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Enviar mensagem</h3>
                                <div class="space-y-3">
                                            <div class="mb-2">
                            <textarea
                                wire:model="newMessage"
                                rows="1"
                                placeholder="Digite sua mensagem aqui..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            ></textarea>
                                                <div class="mt-2 flex justify-end">
                                                    <button
                                                        wire:click="sendMessage"
                                                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                                                    >
                                                        Enviar Mensagem
                                                    </button>
                                                </div>
                                            </div>



                                        </div>
                                <!-- Lista de mensagens -->
                                <div class="h-2/3 overflow-auto">
                                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Historico de Messagens</h3>
                                    @foreach($messages as $message)
                                        <div class="border-l-4
                                    @if($message['type'] === 'status_change') border-yellow-500 bg-yellow-50
                                    @elseif($message['type'] === 'note') border-gray-300 bg-gray-50
                                    @else border-blue-500 bg-blue-50 @endif
                                    pl-4 p-3 rounded-r-lg"
                                        >
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <span class="font-medium text-gray-800">{{ $message['author'] }}</span>
                                                    @if($message['type'] === 'status_change')
                                                        <span class="ml-2 px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                                                    Alteração de Estado
                                                </span>
                                                    @endif
                                                </div>
                                                <div class="text-sm text-gray-500 text-right">
                                                    <div>{{ $message['date'] }}</div>
                                                    <div class="text-xs">{{ $message['time_ago'] }}</div>
                                                </div>
                                            </div>
                                            <div class="text-sm text-gray-700">
                                                {!! nl2br(e($message['content'])) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>



                        </div>
                    </div>
                </div>

            </div>
        </div>


    </div>

    <!-- Estilos específicos -->
    <style>
        body{
            overflow-y: hidden !important;
        }
        .scrollbar-thin {
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 8px;
        }

        .scrollbar-thin::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }

        .scrollbar-thin::-webkit-scrollbar-thumb {
            background-color: #cbd5e0;
            border-radius: 4px;
        }

        /* Estilos para o histórico de mensagens */
        .message-status {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }

        .message-note {
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        }

        .message-normal {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }
    </style>

    <!-- Scripts para interatividade -->
    <script>
        document.addEventListener('livewire:init', () => {
            // Auto-scroll para a última mensagem
            const scrollToBottom = () => {
                const messagesContainer = document.querySelector('.space-y-6');
                if (messagesContainer) {
                    messagesContainer.scrollTop = messagesContainer.scrollHeight;
                }
            };

            // Scroll após cada nova mensagem
            Livewire.hook('message.processed', () => {
                setTimeout(scrollToBottom, 100);
            });

            // Scroll inicial
            setTimeout(scrollToBottom, 300);
        });
    </script>
</div>
