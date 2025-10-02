<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Utilizador') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <dl class="divide-y divide-gray-200">
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-gray-600">Nome</dt>
                        <dd class="text-gray-900">{{ $user->name }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-gray-600">Email</dt>
                        <dd class="text-gray-900">{{ $user->email }}</dd>
                    </div>
                    <div class="py-3 flex justify-between">
                        <dt class="font-medium text-gray-600">Telefone</dt>
                        <dd class="text-gray-900">{{ $user->phone }}</dd>
                    </div>

                    <!-- Gestão de Conta -->
                    <div class="py-3">
                        <dt class="font-medium text-gray-600 mb-2">Gestão de Conta</dt>
                        <dd>
                            <form method="POST" action="{{ route('users.updateStatus', $user) }}" class="space-y-4">
                                @csrf @method('PATCH')

                                <!-- Estado -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Estado</label>
                                    <select name="status"
                                            class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="pending" @selected($user->status=='pending')>Pending</option>
                                        <option value="active" @selected($user->status=='active')>Active</option>
                                        <option value="suspended" @selected($user->status=='suspended')>Suspended</option>
                                    </select>
                                </div>

                                <!-- Plano -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-600">Plano de Subscrição</label>
                                    <select name="plan"
                                            class="mt-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <option value="">-- Nenhum --</option>
                                        <option value="kuma_essencial" @selected(optional($user->subscription)->plan=='kuma_essencial')>
                                            Kuma Essencial
                                        </option>
                                        <option value="kuma_premium" @selected(optional($user->subscription)->plan=='kuma_premium')>
                                            Kuma Premium
                                        </option>
                                    </select>
                                </div>

                                <!-- Datas -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Início</label>
                                        <input type="date" name="start_date"
                                               value="{{ optional($user->subscription)->start_date }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-600">Fim</label>
                                        <input type="date" name="end_date"
                                               value="{{ optional($user->subscription)->end_date }}"
                                               class="mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                </div>

                                <div class="pt-3">
                                    <button type="submit"
                                            class="px-4 py-2 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700">
                                        Atualizar
                                    </button>
                                </div>
                            </form>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
