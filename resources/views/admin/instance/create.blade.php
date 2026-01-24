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
                    <form action="{{ route('instance.store') }}" method="POST">
                        @csrf
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                            Testar agora
                        </button>
                    </form>

                    @livewire('whats-app-connection')

{{--                    <form action="{{ route('instance.connect') }}" method="POST" class="mt-3">--}}
{{--                        @csrf--}}
{{--                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">--}}
{{--                            Conectar agora--}}
{{--                        </button>--}}
{{--                    </form>--}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
