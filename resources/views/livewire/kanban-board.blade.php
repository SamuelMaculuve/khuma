<div class="p-6 bg-gray-50 min-h-screen">
    <!-- Cabeçalho -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Nova</h1>
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-600">Pendentes</h2>
            <div class="flex items-center space-x-4">
                <input
                    type="text"
                    wire:model="newStateName"
                    placeholder="Nome do novo estado"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    wire:keydown.enter="addState"
                >
                <button
                    wire:click="addState"
                    wire:loading.attr="disabled"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-50"
                >
                    <span wire:loading.remove>+ Adicionar Estado</span>
                    <span wire:loading>Adicionando...</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Quadro Kanban -->
    <div class="flex space-x-6 overflow-x-auto pb-6" id="kanban-container">
        @foreach($states as $stateName => $items)
            <div
                class="flex-shrink-0 w-96 bg-white rounded-xl shadow-sm border border-gray-200"
                data-state="{{ $stateName }}"
                ondrop="dropItem(event, '{{ $stateName }}')"
                ondragover="allowDrop(event)"
            >
                <!-- Cabeçalho do Estado -->
                <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold text-gray-800 text-lg">{{ $stateName }}</h3>
                        <span class="text-sm text-gray-500">{{ count($items) }} itens</span>
                    </div>
                    <div class="flex space-x-2">
                        <button
                            wire:click="addNewItem('{{ $stateName }}')"
                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                            title="Adicionar item"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                        @if($stateName !== 'Pendentes')
                            <button
                                wire:click="removeState('{{ $stateName }}')"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Remover estado"
                                onclick="return confirm('Tem certeza que deseja remover este estado? Os itens serão movidos para "Pendentes".')"
                            >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- Itens do Estado -->
                <div
                    class="p-4 space-y-4 min-h-[200px]"
                    id="items-{{ $stateName }}"
                >
                    @foreach($items as $item)
                        <div
                            draggable="true"
                            wire:key="item-{{ $item['id'] }}-{{ $stateName }}"
                            id="item-{{ $item['id'] }}"
                            class="bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-move hover:shadow-md transition-shadow"
                            ondragstart="dragStart(event, {{ $item['id'] }}, '{{ $stateName }}')"
                        >
                            <a href="{{ route('lead.show',$item['id']) }}">
                                <!-- Cabeçalho do Item -->
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $item['title'] }}</h4>
                                    <span class="text-sm font-semibold text-blue-600">#{{ $item['id'] }}</span>

                                </div>

                                <!-- Detalhes do Item -->
                                @if($item['description'])
                                    <p class="text-sm text-gray-600 mb-1">
                                        {{ $item['description'] }}
                                    </p>
                                @endif

                                {{--                            @if($item['description'])--}}
                                {{--                                <div class="flex items-center mb-2">--}}
                                {{--                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">--}}
                                {{--                                        {{ $item['description'] }}--}}
                                {{--                                    </span>--}}
                                {{--                                </div>--}}
                                {{--                            @endif--}}

                                {{--                            @if($item['link'])--}}
                                {{--                                <a href="{{ $item['link'] }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 hover:underline block mb-2">--}}
                                {{--                                    {{ $item['link'] }}--}}
                                {{--                                </a>--}}
                                {{--                            @endif--}}

                                <!-- Rodapé do Item -->
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-xs text-gray-500">{{ $item['time'] }}</span>
                                    <div class="flex items-center text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span class="text-xs">Arraste</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach

                    @if(count($items) === 0)
                        <div class="text-center py-8 text-gray-400 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="w-12 h-12 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                            </svg>
                            <p>Arraste itens aqui</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Instruções -->
    <div class="mt-8 p-4 bg-blue-50 rounded-lg border border-blue-200">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <div>
                <h3 class="font-medium text-blue-800">Como usar:</h3>
                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                    <li>• Arraste itens entre os diferentes estados</li>
                    <li>• Adicione novos estados usando o campo acima</li>
                    <li>• Clique no botão "+" para adicionar novos itens</li>
                    <li>• Clique no "×" para remover estados (exceto "Pendentes")</li>
                </ul>
            </div>
        </div>
    </div>

    <div wire:poll.2s="loadMessages"></div>

    <!-- JavaScript para Drag & Drop -->
    <script>
        let draggedItem = null;
        let draggedFromState = null;
        let draggedItemElement = null;

        function dragStart(event, itemId, fromState) {
            draggedItem = itemId;
            draggedFromState = fromState;
            draggedItemElement = event.target;
            event.dataTransfer.setData('text/plain', itemId);
            event.dataTransfer.effectAllowed = 'move';

            // Adiciona classe visual
            event.target.classList.add('opacity-50', 'border-blue-300');
        }

        function allowDrop(event) {
            event.preventDefault();
            event.dataTransfer.dropEffect = 'move';

            // Adiciona destaque visual à coluna
            event.target.closest('[data-state]')?.classList.add('border-blue-400', 'bg-blue-50');
        }

        function dropItem(event, toState) {
            event.preventDefault();

            // Remove destaque visual
            event.target.closest('[data-state]')?.classList.remove('border-blue-400', 'bg-blue-50');

            if (draggedItem && draggedFromState && draggedFromState !== toState) {
                // Usa o Livewire 3 para chamar o método
                Livewire.dispatch('move-item', {
                    itemId: draggedItem,
                    fromState: draggedFromState,
                    toState: toState
                });

                // Remove a classe visual do item arrastado
                if (draggedItemElement) {
                    draggedItemElement.classList.remove('opacity-50', 'border-blue-300');
                }
            }

            draggedItem = null;
            draggedFromState = null;
            draggedItemElement = null;
        }

        // Remove destaque visual quando o drag termina
        document.addEventListener('dragend', function(event) {
            // Remove destaque de todas as colunas
            document.querySelectorAll('[data-state]').forEach(col => {
                col.classList.remove('border-blue-400', 'bg-blue-50');
            });

            // Remove destaque do item arrastado
            if (draggedItemElement) {
                draggedItemElement.classList.remove('opacity-50', 'border-blue-300');
            }

            draggedItem = null;
            draggedFromState = null;
            draggedItemElement = null;
        });

        // Remove destaque visual quando o drag sai da coluna
        document.addEventListener('dragleave', function(event) {
            if (!event.target.closest('[data-state]')) {
                document.querySelectorAll('[data-state]').forEach(col => {
                    col.classList.remove('border-blue-400', 'bg-blue-50');
                });
            }
        });

        // Configura o Livewire 3 para lidar com o evento de mover item
        document.addEventListener('livewire:init', () => {
            Livewire.on('move-item', (data) => {
            @this.moveItem(data.itemId, data.fromState, data.toState);
            });
        });
    </script>

    <!-- Estilos adicionais -->
    <style>
        [draggable="true"] {
            transition: all 0.2s ease;
        }

        [draggable="true"]:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .drag-over {
            border-color: #3b82f6 !important;
            background-color: #eff6ff !important;
        }

        /* Estilo para scrollbar */
        .overflow-x-auto::-webkit-scrollbar {
            height: 8px;
        }

        .overflow-x-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }

        .overflow-x-auto::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
    </style>
</div>
