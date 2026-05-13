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

                        <!-- Coluna da direita -->
                        <div class="space-y-4">

                            {{-- Send message card --}}
                            <div class="bg-white rounded-lg border border-gray-200 p-5 @if(!$hasChatAccess) blur-sm pointer-events-none select-none @endif overflow-y-auto max-h-[420px] p-4 space-y-3">
                                <h3 class="text-base font-semibold text-gray-800 mb-3">Enviar mensagem</h3>

                                @if(session('error'))
                                    <div class="flex items-center gap-2 px-3 py-2 mb-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                                        {{ session('error') }}
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">Canal</label>
                                    <div class="flex gap-1 flex-wrap">
                                        @foreach(['whatsapp' => 'WhatsApp', 'email' => 'Email', 'sms' => 'SMS', 'phone' => 'Telefone', 'in_person' => 'Presencial'] as $value => $label)
                                            <button type="button"
                                                wire:click="$set('channel', '{{ $value }}')"
                                                class="px-3 py-1 text-xs font-medium rounded-full border transition-colors
                                                       {{ $channel === $value ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
                                                {{ $label }}
                                            </button>
                                        @endforeach
                                    </div>
                                    @if($channel === 'email')
                                        @php $clientEmail = optional($lead->client)->email; @endphp
                                        @if($clientEmail)
                                            <p class="mt-1 text-xs text-green-600">Enviar para: {{ $clientEmail }}</p>
                                        @else
                                            <p class="mt-1 text-xs text-red-500">Cliente sem email registado.</p>
                                        @endif
                                    @endif
                                </div>

                                <textarea
                                    wire:model="newMessage"
                                    rows="3"
                                    placeholder="Digite sua mensagem aqui..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                ></textarea>

                                {{-- File attachment --}}
                                <div class="mt-2">
                                    <label class="flex items-center gap-2 cursor-pointer w-fit">
                                        <div class="flex items-center gap-1.5 px-3 py-1.5 border border-gray-300 rounded-lg text-xs text-gray-600 hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                            Anexar ficheiro
                                        </div>
                                        <input type="file" wire:model="attachment" class="hidden" />
                                    </label>
                                    @if($attachment)
                                        <div class="mt-1 flex items-center gap-2 px-2 py-1 bg-indigo-50 border border-indigo-200 rounded text-xs text-indigo-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            {{ $attachment->getClientOriginalName() }}
                                            <button type="button" wire:click="$set('attachment', null)" class="ml-1 text-indigo-400 hover:text-indigo-700">✕</button>
                                        </div>
                                        @error('attachment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    @endif
                                    <div wire:loading wire:target="attachment" class="mt-1 text-xs text-gray-400">A carregar...</div>
                                </div>

                                <div class="mt-2 flex justify-end">
                                    <button wire:click="sendMessage" wire:loading.attr="disabled"
                                        class="px-5 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                                        <span wire:loading.remove wire:target="sendMessage">Enviar Mensagem</span>
                                        <span wire:loading wire:target="sendMessage">Enviando...</span>
                                    </button>
                                </div>
                            </div>

                            {{-- Message history card --}}
                            <div class="relative bg-white rounded-lg border border-gray-200">
                                <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <h3 class="text-base font-semibold text-gray-800">Histórico de Mensagens</h3>
                                    <span class="text-xs text-gray-400">{{ count($messages) }} mensagem(s)</span>
                                </div>

                                <div wire:poll.5s="loadMessages" class="@if(!$hasChatAccess) blur-sm pointer-events-none select-none @endif overflow-y-auto max-h-[420px] p-4 space-y-3">
                                    @forelse($messages as $message)
                                        <div class="border-l-4 pl-3 py-2 pr-2 rounded-r-lg
                                            @if($message['type'] === 'status_change') border-yellow-400 bg-yellow-50
                                            @elseif($message['type'] === 'note') border-gray-300 bg-gray-50
                                            @elseif(($message['direction'] ?? '') === 'outbound') border-indigo-400 bg-indigo-50
                                            @else border-green-400 bg-green-50 @endif">

                                            <div class="flex justify-between items-start mb-1">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <span class="text-sm font-medium text-gray-800">{{ $message['author'] }}</span>
                                                    @if(($message['direction'] ?? '') === 'outbound')
                                                        <span class="px-1.5 py-0.5 bg-indigo-100 text-indigo-700 text-xs rounded">enviado</span>
                                                    @elseif(($message['direction'] ?? '') === 'inbound')
                                                        <span class="px-1.5 py-0.5 bg-green-100 text-green-700 text-xs rounded">recebido</span>
                                                    @endif
                                                    @if(!empty($message['channel']))
                                                        <span class="px-1.5 py-0.5 bg-gray-100 text-gray-500 text-xs rounded">{{ $message['channel'] }}</span>
                                                    @endif
                                                    @if($message['type'] === 'status_change')
                                                        <span class="px-1.5 py-0.5 bg-yellow-100 text-yellow-700 text-xs rounded">estado</span>
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-400 text-right shrink-0 ml-2">
                                                    <div>{{ $message['time_ago'] }}</div>
                                                </div>
                                            </div>

                                            @if(!empty($message['content']))
                                                <p class="text-sm text-gray-700">{!! nl2br(e($message['content'])) !!}</p>
                                            @endif
                                            @if(!empty($message['attachment']))
                                                <a href="{{ Storage::url($message['attachment']['path']) }}"
                                                   target="_blank"
                                                   class="inline-flex items-center gap-1.5 mt-2 px-3 py-1.5 bg-white border border-gray-300 rounded-lg text-xs text-gray-700 hover:bg-gray-50 transition-colors">
                                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                                    {{ $message['attachment']['name'] }}
                                                    <span class="text-gray-400">({{ round($message['attachment']['size'] / 1024, 1) }} KB)</span>
                                                </a>
                                            @endif
                                        </div>
                                    @empty
                                        <div class="text-center py-8 text-gray-400 text-sm">
                                            Nenhuma mensagem ainda.
                                        </div>
                                    @endforelse
                                </div>

                                @if(!$hasChatAccess)
                                    <div class="absolute inset-0 flex items-center justify-center bg-white/60 backdrop-blur-sm rounded-lg">
                                        <div class="bg-white p-6 rounded-xl shadow-lg text-center max-w-sm">
                                            <h4 class="text-base font-bold mb-2">Chat Premium</h4>
                                            <p class="text-sm text-gray-600 mb-4">Faça upgrade do seu plano para aceder às mensagens.</p>
                                            <a href="#" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 text-sm">Fazer Upgrade</a>
                                        </div>
                                    </div>
                                @endif
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
