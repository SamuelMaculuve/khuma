<div class="py-6">
    <div class="px-4 sm:px-6 lg:px-8">

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

            <div class="p-6">

                <input type="text"
                       wire:model.live="search"
                       placeholder="Pesquisar Cliente..."
                       class="w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 mb-4">

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
                                    {{ $user->email }}
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
</div>
