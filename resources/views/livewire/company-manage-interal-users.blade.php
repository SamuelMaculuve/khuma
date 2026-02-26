<div class="py-6">
    <div class="px-4 sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6">
                <div class="flex justify-between mb-4">
                <input type="text"
                       wire:model.live="search"
                       placeholder="Pesquisar Utilizador..."
                       class="w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm">
                <button wire:click="openModal"
                        class="ml-4 px-2 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    + Novo Utilizador
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                        </thead>

                        <tbody class="bg-white divide-y divide-gray-200">

                        @forelse($users as $user)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $user->name }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->email }}
                                </td>

                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->phone }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $user->address }}
                                </td>

                                <td class="px-6 py-4">
                                    @foreach($user->getRoleNames() as $role)
                                                                        <span class="px-2 py-1 text-xs font-semibold rounded-full
                                                                            {{ $role
                                                                                ? 'bg-green-100 text-green-800'
                                                                                : 'bg-yellow-100 text-yellow-800' }}">
                                                                            {{ ucfirst(\App\Enums\Role::tryFrom($role)->label()) }}
                                                                        </span>
                                    @endforeach
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
                                    Nenhum Utilizador encontrado
                                </td>
                            </tr>
                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>

            </div>
        </div>

    </div>

    @if($showModal)

        <div class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

            <div class="bg-white rounded-xl shadow-lg w-full max-w-lg p-6">

                <h2 class="text-lg font-bold mb-4">
                    Adicionar novo Utilizador
                </h2>

                <div class="space-y-4">

                    <input wire:model="name"
                           placeholder="Nome"
                           class="w-full border rounded-md p-2">

                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror


                    <input wire:model="email"
                           placeholder="Email"
                           class="w-full border rounded-md p-2">

                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror


                    <input wire:model="phone"
                           placeholder="Telefone"
                           class="w-full border rounded-md p-2">

                    <select wire:model="role" class="w-full border rounded-md p-2">

                        @foreach(\App\Enums\Role::cases() as $role)
                            @if($role != \App\Enums\Role::ADMIN)
                            <option value="{{ $role->value }}">
                                {{ $role->label() }}
                            </option>
                            @endif
                        @endforeach

                    </select>

                </div>

                <div class="flex justify-end mt-6 space-x-2">

                    <button wire:click="closeModal"
                            class="px-4 py-2 bg-gray-300 rounded-lg">
                        Cancelar
                    </button>

                    <button wire:click="save"
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg">
                        Guardar
                    </button>

                </div>

            </div>

        </div>

    @endif


</div>
