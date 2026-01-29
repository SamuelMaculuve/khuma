<div>
{{--    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8" style="height: 90vh;">--}}
{{--        <!-- Modal de escolha de método -->--}}
{{--        @if($showMethodModal && $selectedContact)--}}
{{--            <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" id="methodModal">--}}
{{--                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">--}}
{{--                    <div class="mt-3 text-center">--}}
{{--                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">--}}
{{--                            <i class="fas fa-paper-plane text-blue-600 text-xl"></i>--}}
{{--                        </div>--}}

{{--                        <h3 class="text-lg font-medium text-gray-900 mb-2">Escolher método de envio</h3>--}}

{{--                        <p class="text-sm text-gray-500 mb-6">--}}
{{--                            Como deseja enviar esta mensagem para {{ $selectedContact['name'] }}?--}}
{{--                        </p>--}}

{{--                        <!-- Opções de método -->--}}
{{--                        <div class="grid grid-cols-2 gap-4 mb-6">--}}
{{--                            <!-- WhatsApp -->--}}
{{--                            <button wire:click="selectMethod('whatsapp')"--}}
{{--                                    @if(!$selectedContact['whatsapp_available']) disabled @endif--}}
{{--                                    class="p-4 border rounded-lg hover:bg-gray-50 flex flex-col items-center--}}
{{--                                       @if($selectedContact['whatsapp_available']) cursor-pointer--}}
{{--                                       @else opacity-50 cursor-not-allowed @endif--}}
{{--                                       @if($selectedMethod === 'whatsapp') border-green-500 bg-green-50 @endif">--}}
{{--                                <i class="fab fa-whatsapp text-2xl mb-2--}}
{{--                                      @if($selectedContact['whatsapp_available']) text-green-500--}}
{{--                                      @else text-gray-400 @endif"></i>--}}
{{--                                <span class="font-medium">WhatsApp</span>--}}
{{--                                @if(!$selectedContact['whatsapp_available'])--}}
{{--                                    <span class="text-xs text-red-500 mt-1">Indisponível</span>--}}
{{--                                @endif--}}
{{--                            </button>--}}

{{--                            <!-- SMS -->--}}
{{--                            <button wire:click="selectMethod('sms')"--}}
{{--                                    @if(!$selectedContact['sms_available']) disabled @endif--}}
{{--                                    class="p-4 border rounded-lg hover:bg-gray-50 flex flex-col items-center--}}
{{--                                       @if($selectedContact['sms_available']) cursor-pointer--}}
{{--                                       @else opacity-50 cursor-not-allowed @endif--}}
{{--                                       @if($selectedMethod === 'sms') border-blue-500 bg-blue-50 @endif">--}}
{{--                                <i class="fas fa-sms text-2xl mb-2--}}
{{--                                      @if($selectedContact['sms_available']) text-blue-500--}}
{{--                                      @else text-gray-400 @endif"></i>--}}
{{--                                <span class="font-medium">SMS</span>--}}
{{--                                @if(!$selectedContact['sms_available'])--}}
{{--                                    <span class="text-xs text-red-500 mt-1">Indisponível</span>--}}
{{--                                @endif--}}
{{--                            </button>--}}
{{--                        </div>--}}

{{--                        <div class="flex justify-between mt-4">--}}
{{--                            <button wire:click="cancelMessage"--}}
{{--                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100--}}
{{--                                       hover:bg-gray-200 rounded-md">--}}
{{--                                Cancelar--}}
{{--                            </button>--}}

{{--                            <button wire:click="sendMessageConfirmed"--}}
{{--                                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600--}}
{{--                                       hover:bg-blue-700 rounded-md">--}}
{{--                                Enviar agora--}}
{{--                            </button>--}}
{{--                        </div>--}}

{{--                        <div class="mt-4 text-xs text-gray-500 text-left">--}}
{{--                            <p><strong>Pré-visualização:</strong></p>--}}
{{--                            <div class="mt-1 p-2 bg-gray-100 rounded max-h-20 overflow-y-auto">--}}
{{--                                {{ $tempMessage }}--}}
{{--                            </div>--}}
{{--                            <p class="mt-2">--}}
{{--                                <i class="fas fa-info-circle mr-1"></i>--}}
{{--                                @if($selectedMethod === 'whatsapp')--}}
{{--                                    Será enviado via WhatsApp (dados móveis necessários)--}}
{{--                                @else--}}
{{--                                    Será enviado via SMS (custo pode ser aplicado)--}}
{{--                                @endif--}}
{{--                            </p>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

{{--        <div class="bg-white rounded-xl shadow overflow-hidden h-full">--}}
{{--            <div class="flex h-full">--}}
{{--                <!-- Coluna da lista de contatos -->--}}
{{--                <div class="w-1/3 border-r border-gray-200 flex flex-col">--}}
{{--                    <div class="p-4 border-b border-gray-200 shrink-0">--}}
{{--                        <h2 class="text-xl font-semibold text-gray-800">Conversas</h2>--}}
{{--                        <div class="mt-2 relative">--}}
{{--                            <input type="text"--}}
{{--                                   placeholder="Procurar contato..."--}}
{{--                                   class="w-full p-2 border border-gray-300 rounded-lg pl-10">--}}
{{--                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="flex-1 overflow-y-auto">--}}
{{--                        @foreach($contacts as $contact)--}}
{{--                            <div wire:click="selectContact({{ $contact['id'] }})"--}}
{{--                                 class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer--}}
{{--                                    {{ $selectedContact && $selectedContact['id'] == $contact['id'] ? 'bg-blue-50' : '' }}">--}}
{{--                                <div class="flex items-center">--}}
{{--                                    <div class="relative">--}}
{{--                                        <img class="h-12 w-12 rounded-full object-cover"--}}
{{--                                             src="{{ $contact['avatar'] }}"--}}
{{--                                             alt="{{ $contact['name'] }}">--}}
{{--                                        @if($contact['status'] == 'online')--}}
{{--                                            <span class="absolute bottom-0 right-0 h-3 w-3 bg-green-500 rounded-full border-2 border-white"></span>--}}
{{--                                        @endif--}}
{{--                                    </div>--}}
{{--                                    <div class="ml-4 flex-1">--}}
{{--                                        <div class="flex justify-between">--}}
{{--                                            <p class="text-sm font-medium text-gray-900">{{ $contact['name'] }}</p>--}}
{{--                                            <span class="text-xs text-gray-500">{{ $contact['last_seen'] }}</span>--}}
{{--                                        </div>--}}
{{--                                        <p class="text-sm text-gray-500">{{ $contact['phone'] }}</p>--}}

{{--                                        <!-- Indicadores de métodos disponíveis -->--}}
{{--                                        <div class="flex space-x-2 mt-1">--}}
{{--                                            @if($contact['whatsapp_available'])--}}
{{--                                                <span class="text-xs text-green-600">--}}
{{--                                                <i class="fab fa-whatsapp"></i> WhatsApp--}}
{{--                                            </span>--}}
{{--                                            @endif--}}
{{--                                            @if($contact['sms_available'])--}}
{{--                                                <span class="text-xs text-blue-600">--}}
{{--                                                <i class="fas fa-sms"></i> SMS--}}
{{--                                            </span>--}}
{{--                                            @endif--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        @endforeach--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <!-- Coluna da conversa -->--}}
{{--                <div class="w-2/3 flex flex-col">--}}
{{--                    @if($selectedContact)--}}
{{--                        <!-- Cabeçalho da conversa -->--}}
{{--                        <div class="p-4 border-b border-gray-200 flex items-center justify-between shrink-0">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <img class="h-10 w-10 rounded-full object-cover"--}}
{{--                                     src="{{ $selectedContact['avatar'] }}"--}}
{{--                                     alt="{{ $selectedContact['name'] }}">--}}
{{--                                <div class="ml-4">--}}
{{--                                    <p class="text-sm font-medium text-gray-900">{{ $selectedContact['name'] }}</p>--}}
{{--                                    <p class="text-sm text-gray-500">--}}
{{--                                        @if($selectedContact['status'] == 'online')--}}
{{--                                            <span class="text-green-500">● Online</span>--}}
{{--                                        @else--}}
{{--                                            Última vez {{ $selectedContact['last_seen'] }}--}}
{{--                                        @endif--}}

{{--                                        <!-- Métodos disponíveis no cabeçalho -->--}}
{{--                                        <span class="ml-3">--}}
{{--                                        @if($selectedContact['whatsapp_available'])--}}
{{--                                                <i class="fab fa-whatsapp text-green-500" title="WhatsApp disponível"></i>--}}
{{--                                            @endif--}}
{{--                                            @if($selectedContact['sms_available'])--}}
{{--                                                <i class="fas fa-sms text-blue-500 ml-2" title="SMS disponível"></i>--}}
{{--                                            @endif--}}
{{--                                    </span>--}}
{{--                                    </p>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                            <div class="flex space-x-4">--}}
{{--                                <button wire:click="startCall({{ $selectedContact['id'] }})"--}}
{{--                                        class="p-2 rounded-full hover:bg-gray-100"--}}
{{--                                        title="Fazer chamada">--}}
{{--                                    <i class="fas fa-phone text-green-600"></i>--}}
{{--                                </button>--}}
{{--                                <button class="p-2 rounded-full hover:bg-gray-100"--}}
{{--                                        title="Informações">--}}
{{--                                    <i class="fas fa-info-circle text-blue-600"></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Área de chamada ativa -->--}}
{{--                        @if($callStatus)--}}
{{--                            <div class="bg-blue-50 p-4 border-b border-blue-100 shrink-0">--}}
{{--                                <div class="flex items-center justify-between">--}}
{{--                                    <div class="flex items-center">--}}
{{--                                        <i class="fas fa-phone text-green-600 mr-3"></i>--}}
{{--                                        <div>--}}
{{--                                            <p class="font-medium text-gray-800">--}}
{{--                                                @if($callStatus == 'calling')--}}
{{--                                                    Chamando {{ $selectedContact['name'] }}...--}}
{{--                                                @elseif($callStatus == 'ended')--}}
{{--                                                    Chamada terminada--}}
{{--                                                @endif--}}
{{--                                            </p>--}}
{{--                                            <p class="text-sm text-gray-600">{{ $selectedContact['phone'] }}</p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    @if($callStatus == 'calling')--}}
{{--                                        <button wire:click="endCall"--}}
{{--                                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">--}}
{{--                                            <i class="fas fa-phone-slash mr-2"></i>Terminar--}}
{{--                                        </button>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                                @if($callStatus == 'calling')--}}
{{--                                    <div class="mt-2 text-sm text-gray-600">--}}
{{--                                        <i class="fas fa-volume-up mr-1"></i> 00:32 de duração--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        @endif--}}

{{--                        <!-- Área de mensagens com scroll -->--}}
{{--                        <div class="flex-1 overflow-y-auto" id="messagesContainer">--}}
{{--                            <div class="p-4 space-y-4">--}}
{{--                                @foreach($messages as $msg)--}}
{{--                                    @if($msg['type'] == 'sent')--}}
{{--                                        <div class="flex justify-end">--}}
{{--                                            <div class="max-w-xs lg:max-w-md">--}}
{{--                                                <div class="bg-blue-100 text-gray-800 rounded-lg p-3">--}}
{{--                                                    <p class="break-words">{{ $msg['text'] }}</p>--}}
{{--                                                    <div class="flex justify-between items-center mt-1">--}}
{{--                                                        <span class="text-xs text-gray-500">{{ $msg['time'] }}</span>--}}

{{--                                                        <!-- Indicador de método de envio -->--}}
{{--                                                        @if($msg['method'])--}}
{{--                                                            <span class="text-xs ml-2 flex items-center">--}}
{{--                                                            <i class="{{ $this->getMethodIcon($msg['method']) }} mr-1"></i>--}}
{{--                                                            {{ $this->getMethodText($msg['method']) }}--}}
{{--                                                        </span>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @else--}}
{{--                                        <div class="flex justify-start">--}}
{{--                                            <div class="max-w-xs lg:max-w-md">--}}
{{--                                                <div class="bg-white text-gray-800 rounded-lg p-3 shadow-sm">--}}
{{--                                                    <p class="break-words">{{ $msg['text'] }}</p>--}}
{{--                                                    <div class="flex justify-between items-center mt-1">--}}
{{--                                                        <span class="text-xs text-gray-500">{{ $msg['time'] }}</span>--}}

{{--                                                        <!-- Para mensagens recebidas, mostrar de onde veio -->--}}
{{--                                                        @if($msg['method'])--}}
{{--                                                            <span class="text-xs ml-2 flex items-center">--}}
{{--                                                            <i class="{{ $this->getMethodIcon($msg['method']) }} mr-1"></i>--}}
{{--                                                            Recebido via {{ $this->getMethodText($msg['method']) }}--}}
{{--                                                        </span>--}}
{{--                                                        @else--}}
{{--                                                            <span class="text-xs ml-2 text-gray-400">--}}
{{--                                                            Mensagem recebida--}}
{{--                                                        </span>--}}
{{--                                                        @endif--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                @endforeach--}}
{{--                            </div>--}}
{{--                        </div>--}}

{{--                        <!-- Input para enviar mensagem -->--}}
{{--                        <div class="p-4 border-t border-gray-200 shrink-0">--}}
{{--                            <div class="flex items-center">--}}
{{--                                <button class="p-2 text-gray-500 hover:text-gray-700">--}}
{{--                                    <i class="fas fa-paperclip"></i>--}}
{{--                                </button>--}}

{{--                                <!-- Método de envio rápido -->--}}
{{--                                <div class="flex mr-2">--}}
{{--                                    <button wire:click="$set('selectedMethod', 'whatsapp')"--}}
{{--                                            class="p-2 rounded-l-lg border border-gray-300--}}
{{--                                               @if($selectedMethod === 'whatsapp') bg-green-50 border-green-300 @endif--}}
{{--                                               @if(!$selectedContact['whatsapp_available']) opacity-50 cursor-not-allowed @endif"--}}
{{--                                            @if(!$selectedContact['whatsapp_available']) disabled @endif--}}
{{--                                            title="Enviar via WhatsApp">--}}
{{--                                        <i class="fab fa-whatsapp--}}
{{--                                              @if($selectedMethod === 'whatsapp') text-green-600--}}
{{--                                              @else text-green-400 @endif"></i>--}}
{{--                                    </button>--}}

{{--                                    <button wire:click="$set('selectedMethod', 'sms')"--}}
{{--                                            class="p-2 rounded-r-lg border border-gray-300 border-l-0--}}
{{--                                               @if($selectedMethod === 'sms') bg-blue-50 border-blue-300 @endif--}}
{{--                                               @if(!$selectedContact['sms_available']) opacity-50 cursor-not-allowed @endif"--}}
{{--                                            @if(!$selectedContact['sms_available']) disabled @endif--}}
{{--                                            title="Enviar via SMS">--}}
{{--                                        <i class="fas fa-sms--}}
{{--                                              @if($selectedMethod === 'sms') text-blue-600--}}
{{--                                              @else text-blue-400 @endif"></i>--}}
{{--                                    </button>--}}
{{--                                </div>--}}

{{--                                <div class="flex-1 mx-2">--}}
{{--                                <textarea--}}
{{--                                    wire:model="message"--}}
{{--                                    wire:keydown.enter.prevent="sendMessage"--}}
{{--                                    placeholder="Digite uma mensagem..."--}}
{{--                                    rows="1"--}}
{{--                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"--}}
{{--                                    style="max-height: 120px; min-height: 44px; overflow-y: auto;"--}}
{{--                                    oninput="this.style.height = 'auto'; this.style.height = (this.scrollHeight) + 'px';"--}}
{{--                                ></textarea>--}}
{{--                                </div>--}}

{{--                                <button wire:click="sendMessage"--}}
{{--                                        class="p-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700--}}
{{--                                           flex items-center ml-2">--}}
{{--                                    <i class="fas fa-paper-plane"></i>--}}
{{--                                </button>--}}
{{--                            </div>--}}

{{--                            <div class="mt-2 flex flex-col sm:flex-row justify-between items-start sm:items-center">--}}
{{--                                <div class="text-xs text-gray-500 mb-2 sm:mb-0">--}}
{{--                                    <i class="fas fa-info-circle mr-1"></i>--}}
{{--                                    <span class="hidden sm:inline">Pressione Enter para enviar</span>--}}
{{--                                    <span class="sm:ml-2">--}}
{{--                                    Método atual:--}}
{{--                                    <span class="font-medium--}}
{{--                                          @if($selectedMethod === 'whatsapp') text-green-600--}}
{{--                                          @else text-blue-600 @endif">--}}
{{--                                        {{ $selectedMethod === 'whatsapp' ? 'WhatsApp' : 'SMS' }}--}}
{{--                                    </span>--}}
{{--                                </span>--}}
{{--                                </div>--}}

{{--                                <div class="text-xs text-gray-500">--}}
{{--                                    @if($selectedMethod === 'sms')--}}
{{--                                        <i class="fas fa-money-bill-wave mr-1"></i> Custo de SMS pode ser aplicado--}}
{{--                                    @else--}}
{{--                                        <i class="fas fa-wifi mr-1"></i> Requer conexão de dados--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @else--}}
{{--                        <!-- Tela inicial quando nenhum contato está selecionado -->--}}
{{--                        <div class="flex-1 flex items-center justify-center overflow-y-auto">--}}
{{--                            <div class="text-center p-4">--}}
{{--                                <div class="mx-auto h-24 w-24 rounded-full bg-blue-100 flex items-center justify-center mb-4">--}}
{{--                                    <i class="fas fa-comments text-blue-600 text-3xl"></i>--}}
{{--                                </div>--}}
{{--                                <h3 class="text-lg font-medium text-gray-900">Selecione uma conversa</h3>--}}
{{--                                <p class="mt-1 text-sm text-gray-500 mb-6">--}}
{{--                                    Escolha um contato para começar a conversar--}}
{{--                                </p>--}}

{{--                                <!-- Dicas sobre métodos de envio -->--}}
{{--                                <div class="mt-6 bg-gray-50 p-4 rounded-lg max-w-md mx-auto">--}}
{{--                                    <h4 class="font-medium text-gray-800 mb-2">Métodos de envio disponíveis:</h4>--}}
{{--                                    <div class="flex justify-center space-x-6">--}}
{{--                                        <div class="text-center">--}}
{{--                                            <i class="fab fa-whatsapp text-2xl text-green-500 mb-1"></i>--}}
{{--                                            <p class="text-xs text-gray-600">WhatsApp</p>--}}
{{--                                            <p class="text-xs text-gray-500">Gratuito (dados)</p>--}}
{{--                                        </div>--}}
{{--                                        <div class="text-center">--}}
{{--                                            <i class="fas fa-sms text-2xl text-blue-500 mb-1"></i>--}}
{{--                                            <p class="text-xs text-gray-600">SMS</p>--}}
{{--                                            <p class="text-xs text-gray-500">Pode ter custo</p>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                </div>--}}

{{--                                <div class="mt-6">--}}
{{--                                    <button wire:click="selectContact(1)"--}}
{{--                                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">--}}
{{--                                        <i class="fas fa-comment mr-2"></i>--}}
{{--                                        Começar conversa de exemplo--}}
{{--                                    </button>--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    @endif--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    @push('scripts')--}}
{{--        <script>--}}
{{--            // Scroll automático para baixo das mensagens--}}
{{--            window.addEventListener('scrollToBottom', () => {--}}
{{--                const container = document.getElementById('messagesContainer');--}}
{{--                if (container) {--}}
{{--                    container.scrollTop = container.scrollHeight;--}}
{{--                }--}}
{{--            });--}}

{{--            // Auto-resize do textarea--}}
{{--            document.addEventListener('DOMContentLoaded', function() {--}}
{{--                const textarea = document.querySelector('textarea[wire\\:model="message"]');--}}
{{--                if (textarea) {--}}
{{--                    textarea.addEventListener('input', function() {--}}
{{--                        this.style.height = 'auto';--}}
{{--                        this.style.height = (this.scrollHeight) + 'px';--}}
{{--                    });--}}
{{--                }--}}
{{--            });--}}

{{--            // Toque de chamada--}}
{{--            window.addEventListener('callStarted', (e) => {--}}
{{--                const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-phone-ring-1092.mp3');--}}
{{--                audio.loop = true;--}}
{{--                audio.play();--}}

{{--                setTimeout(() => {--}}
{{--                    audio.pause();--}}
{{--                }, 3000);--}}
{{--            });--}}

{{--            // Notificações--}}
{{--            window.addEventListener('showNotification', (e) => {--}}
{{--                const { type, message } = e.detail;--}}

{{--                // Criar notificação--}}
{{--                const notification = document.createElement('div');--}}
{{--                notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50--}}
{{--                                ${type === 'success' ? 'bg-green-100 text-green-800 border-green-200' :--}}
{{--                    type === 'error' ? 'bg-red-100 text-red-800 border-red-200' :--}}
{{--                        'bg-blue-100 text-blue-800 border-blue-200'}`;--}}
{{--                notification.innerHTML = `--}}
{{--            <div class="flex items-center">--}}
{{--                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} mr-2"></i>--}}
{{--                <span>${message}</span>--}}
{{--            </div>--}}
{{--        `;--}}

{{--                document.body.appendChild(notification);--}}

{{--                // Remover após 3 segundos--}}
{{--                setTimeout(() => {--}}
{{--                    notification.remove();--}}
{{--                }, 3000);--}}
{{--            });--}}

{{--            // Fechar modal ao clicar fora--}}
{{--            document.addEventListener('click', (e) => {--}}
{{--                const modal = document.getElementById('methodModal');--}}
{{--                if (modal && e.target === modal) {--}}
{{--                @this.set('showMethodModal', false);--}}
{{--                @this.set('tempMessage', '');--}}
{{--                }--}}
{{--            });--}}

{{--            // Prevenir envio com Enter se modal estiver aberto--}}
{{--            document.addEventListener('keydown', (e) => {--}}
{{--                if (e.key === 'Enter' && @this.showMethodModal) {--}}
{{--                    e.preventDefault();--}}
{{--                }--}}
{{--            });--}}

{{--            // Inicializar scroll ao carregar--}}
{{--            Livewire.hook('component.initialized', (component) => {--}}
{{--                setTimeout(() => {--}}
{{--                    const container = document.getElementById('messagesContainer');--}}
{{--                    if (container) {--}}
{{--                        container.scrollTop = container.scrollHeight;--}}
{{--                    }--}}
{{--                }, 100);--}}
{{--            });--}}
{{--        </script>--}}
{{--    @endpush--}}
</div>
