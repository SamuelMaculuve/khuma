<div>
    <div class="p-6 bg-gray-50 min-h-screen">
{{--    <div class="p-6 bg-gray-50 min-h-screen" x-data x-on:dragend.window="$wire.endDrag('{{ $stateName }}')">--}}
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
                        x-on:keydown.enter="$wire.addState()"
                    >
                    <button
                        wire:click="addState"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors"
                    >
                        + Adicionar Estado
                    </button>
                </div>
            </div>
        </div>

        <!-- Quadro Kanban -->
        <div class="flex space-x-6 overflow-x-auto pb-6"
             x-on:dragover.prevent
             x-on:dragenter.prevent>

            @foreach($states as $stateName => $items)
                <div
                    class="flex-shrink-0 w-96 bg-white rounded-xl shadow-sm border border-gray-200"
                    x-on:drop="$wire.endDrag('{{ $stateName }}')"
                    x-on:dragover.prevent
                    x-on:dragenter.prevent
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
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    <!-- Itens do Estado -->
                    <div class="p-4 space-y-4 min-h-[200px]">
                        @foreach($items as $item)
                            <div
                                draggable="true"
                                wire:key="item-{{ $item['id'] }}-{{ $stateName }}"
                                x-on:dragstart="$wire.startDrag({{ $item['id'] }}, '{{ $stateName }}')"
                                class="bg-gray-50 border border-gray-200 rounded-lg p-4 cursor-move hover:shadow-md transition-shadow"
                                :class="{ 'opacity-50': $wire.draggingItem == {{ $item['id'] }} }"
                            >
                                <!-- Cabeçalho do Item -->
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-gray-900">{{ $item['title'] }}</h4>
                                    <span class="text-sm font-semibold text-blue-600">#{{ $item['number'] }}</span>
                                </div>

                                <!-- Detalhes do Item -->
                                @if($item['requester'])
                                    <p class="text-sm text-gray-600 mb-1">
                                        {{ $item['requester'] }}
                                    </p>
                                @endif

                                @if($item['service'])
                                    <div class="flex items-center mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $item['icon'] === 'fenix' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ $item['service'] }}
                                    </span>
                                    </div>
                                @endif

                                @if($item['link'])
                                    <a href="{{ $item['link'] }}" target="_blank" class="text-sm text-blue-500 hover:text-blue-700 hover:underline block mb-2">
                                        {{ $item['link'] }}
                                    </a>
                                @endif

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

        <!-- Estilos adicionais -->
        <style>
            [draggable="true"] {
                user-select: none;
                -webkit-user-drag: element;
            }

            .drag-over {
                background-color: #f0f9ff;
                border-color: #3b82f6;
            }
        </style>

        <!-- Script para melhorar o drag & drop -->
        <script>
            document.addEventListener('livewire:load', function() {
                // Adiciona classes durante o drag
                document.addEventListener('dragstart', function(e) {
                    if (e.target.draggable) {
                        e.target.classList.add('opacity-50');
                    }
                });

                document.addEventListener('dragend', function(e) {
                    if (e.target.draggable) {
                        e.target.classList.remove('opacity-50');
                    }
                });
            });
        </script>
    </div>
</div>
