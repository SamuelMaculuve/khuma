<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Estatísticas -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Visão Geral</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    <!-- Total Chamadas -->
                    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Total Chamadas</span>
                            <span class="bg-indigo-100 text-indigo-600 text-xs px-2 py-1 rounded-full">📞</span>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-gray-800">{{ $totalCalls }}</p>
                    </div>

                    <!-- Duração Total -->
                    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Duração Total</span>
                            <span class="bg-blue-100 text-blue-600 text-xs px-2 py-1 rounded-full">⏱️</span>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-gray-800">{{ $totalDuration }} seg</p>
                    </div>

                    <!-- Chamadas Perdidas -->
                    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Chamadas Perdidas</span>
                            <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full">❌</span>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-red-600">{{ $missedCalls }}</p>
                    </div>

                    <!-- Duração Média -->
                    <div class="bg-white p-6 rounded-xl shadow hover:shadow-md transition">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Duração Média</span>
                            <span class="bg-green-100 text-green-600 text-xs px-2 py-1 rounded-full">📊</span>
                        </div>
                        <p class="mt-3 text-3xl font-bold text-gray-800">
                            {{ number_format($avgDuration, 1) }} seg
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Chamadas Recentes</h3>

                <div class="bg-white rounded-xl shadow overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Duração</th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentCalls as $call)
                            <tr>
                                <td class="px-4 py-2 text-sm text-gray-800">{{ $call->number }}</td>
                                <td class="px-4 py-2">
                                    @if($call->type === 'INCOMING')
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">Recebida</span>
                                    @elseif($call->type === 'OUTGOING')
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Efetuada</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800">Perdida</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">
                                    {{ optional($call->started_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $call->duration_seconds }} seg</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">
                                    Nenhuma chamada registada.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-right">
                    <a href="{{ route('call_logs.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700">
                        Ver mais
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!-- Lista de Chamadas Recentes -->

</x-app-layout>
