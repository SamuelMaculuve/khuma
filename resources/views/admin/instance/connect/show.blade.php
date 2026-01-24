<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Criar instancia') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <div class="mt-4 text-right">
                    <h1 class="text-xl font-bold mb-4 text-center">
                        Uazapi – Estado da Instância
                    </h1>

                    {{-- STATUS --}}
                    <div class="mb-4">
                        <p><strong>Nome:</strong> {{ $data['instance']['name'] }}</p>
                        <p><strong>Status:</strong>
                            <span class="px-2 py-1 rounded text-white
                {{ $data['connected'] ? 'bg-green-500' : 'bg-yellow-500' }}">
                {{ $data['response'] }}
            </span>
                        </p>
                        <p><strong>Conectado:</strong> {{ $data['connected'] ? 'Sim' : 'Não' }}</p>
                        <p><strong>Última desconexão:</strong> {{ $data['instance']['lastDisconnect'] }}</p>
                        <p><strong>Motivo:</strong> {{ $data['instance']['lastDisconnectReason'] }}</p>
                    </div>

                    {{-- QR CODE --}}
                    @if(!empty($data['instance']['qrcode']))
                        <div class="text-center mt-6">
                            <p class="mb-2 font-semibold">Escaneie o QR Code</p>
                            <img
                                src="{{ $data['instance']['qrcode'] }}"
                                alt="QR Code"
                                class="mx-auto border rounded-lg p-2"
                            >
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
