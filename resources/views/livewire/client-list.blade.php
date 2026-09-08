<div class="py-6">
    <div class="px-4 sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6">

                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <input type="text"
                           wire:model.live="search"
                           placeholder="Pesquisar Cliente..."
                           class="w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <button wire:click="openCreateClient"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-medium">
                        + Novo Cliente
                    </button>
                </div>

                <div class="overflow-x-auto">

                    <table class="w-full border border-gray-200 rounded-lg divide-y divide-gray-200">

                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Telefone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Endereco</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estado</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($clients as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($editingClientId === $user->id)
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="email"
                                                wire:model="editingClientEmail"
                                                class="rounded border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500 w-44"
                                                placeholder="email@exemplo.com"
                                            />
                                            <button wire:click="saveEmail" class="text-green-600 hover:text-green-800 text-xs font-medium">Guardar</button>
                                            <button wire:click="cancelEditEmail" class="text-gray-400 hover:text-gray-600 text-xs">Cancelar</button>
                                        </div>
                                        @error('editingClientEmail')
                                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                        @enderror
                                    @else
                                        <span>{{ $user->email ?: '—' }}</span>
                                        <button
                                            wire:click="startEditEmail({{ $user->id }})"
                                            class="ml-1 text-indigo-400 hover:text-indigo-600"
                                            title="Editar email"
                                        >
                                            <svg class="w-3.5 h-3.5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        </button>
                                    @endif
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->phone }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->address }}
                                </td>

                                <td class="px-6 py-4">
{{--                                    <span class="px-2 py-1 text-xs font-semibold rounded-full--}}
{{--                                        {{ $user->status === 'active'--}}
{{--                                            ? 'bg-green-100 text-green-800'--}}
{{--                                            : 'bg-yellow-100 text-yellow-800' }}">--}}
{{--                                        {{ ucfirst($user->status) }}--}}
{{--                                    </span>--}}
                                </td>

                                <td class="px-6 py-4 text-right">
                                    <a href="#"
                                       class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        Detalhes
                                    </a>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-6 text-gray-500">
                                    Nenhum Cliente encontrado
                                </td>
                            </tr>
                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-4">
                    {{ $clients->links() }}
                </div>

            </div>
        </div>

    </div>

    @if($showCreateClient)
        <div
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
            wire:click.self="closeCreateClient"
            wire:key="new-client-modal"
        >
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-800">Novo Cliente</h3>
                    <button wire:click="closeCreateClient" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="createClient" class="px-6 py-5 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome *</label>
                        <input type="text" wire:model="new_name" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Nome do cliente">
                        @error('new_name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="new_email" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="email@exemplo.com">
                            @error('new_email') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Telefone</label>
                            <input type="text" wire:model="new_phone" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="+258 ...">
                            @error('new_phone') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Endereço</label>
                        <textarea wire:model="new_address" rows="2" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Endereço..."></textarea>
                        @error('new_address') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeCreateClient" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                            Cancelar
                        </button>
                        <button type="submit" wire:loading.attr="disabled" wire:target="createClient" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50">
                            <span wire:loading.remove wire:target="createClient">Salvar Cliente</span>
                            <span wire:loading wire:target="createClient">Salvando...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
