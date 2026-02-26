<div>
    <x-app-layout>
        <x-slot name="header">
            {{--        <h2 class="font-semibold text-xl text-gray-800 leading-tight">--}}
            {{--            {{ __('Leads') }}--}}
            {{--        </h2>--}}
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <button wire:click="showKanban" class="{{ $view === 'kanban' ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'}}">
                        {{ __('Todos Leads') }}
                    </button>
                </div>

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <button wire:click="showClientes" class="{{ $view === 'clientes' ? 'inline-flex items-center px-1 pt-1 border-b-2 border-indigo-400 text-sm font-medium leading-5 text-gray-900 focus:outline-none focus:border-indigo-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out'}}">
                        {{ __('Clientes') }}
                    </button>
                </div>

            </div>
        </x-slot>

        @if($view === 'kanban')
            <livewire:kanban-board />
        @endif

        @if($view === 'clientes')
            <livewire:client-list />
        @endif

    </x-app-layout>

</div>
