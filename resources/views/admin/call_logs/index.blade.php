<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Chamadas') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">

                    <!-- Versão Desktop (Tabela) -->
                    <div class="hidden lg:block">
                        <table id="calls-table" class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Número</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Nome</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Tipo</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Início</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Duração</th>
                            </tr>
                            </thead>
                        </table>
                    </div>

                    <!-- Versão Mobile (Cartões) -->
                    <div id="calls-cards" class="grid grid-cols-1 gap-4 lg:hidden">
                        <!-- Preenchido via Ajax -->
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- jQuery + DataTables -->
{{--    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>--}}
{{--    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">--}}
{{--    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>--}}

    <script>
        $(function () {
            // --- Desktop (DataTable normal) ---
            $('#calls-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('call_logs.index') }}",
                columns: [
                    {data: 'number', name: 'number'},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'started_at', name: 'started_at'},
                    {data: 'duration_fmt', name: 'duration_fmt'},
                ],
                language: {
                    url: '{{ asset('json/Portuguese.json') }}'
                }
            });

            // --- Mobile (Cartões) ---
            $.ajax({
                url: "{{ route('call_logs.index') }}",
                data: { length: 10 }, // limitar número de registos por request
                success: function (data) {
                    let container = $('#calls-cards');
                    container.empty();

                    data.data.forEach(call => {
                        let badgeClass = 'bg-gray-100 text-gray-800';
                        let badgeLabel = call.type;

                        if (call.type === 'INCOMING') {
                            badgeClass = 'bg-green-100 text-green-800';
                            badgeLabel = 'Recebida';
                        } else if (call.type === 'OUTGOING') {
                            badgeClass = 'bg-blue-100 text-blue-800';
                            badgeLabel = 'Efetuada';
                        } else if (call.type === 'MISSED') {
                            badgeClass = 'bg-red-100 text-red-800';
                            badgeLabel = 'Perdida';
                        }

                        container.append(`
                            <div class="bg-white shadow rounded-lg p-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm text-gray-500">Número</p>
                                        <p class="text-lg font-semibold text-gray-900">${call.number ?? '-'}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${badgeClass}">
                                        ${badgeLabel}
                                    </span>
                                </div>
                                <div class="mt-3 grid grid-cols-2 gap-2 text-sm text-gray-600">
                                    <div>
                                        <p class="text-xs text-gray-500">Nome</p>
                                        <p>${call.name ?? '-'}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Duração</p>
                                        <p>${call.duration_fmt}</p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-xs text-gray-500">Início</p>
                                        <p>${call.started_at}</p>
                                    </div>
                                </div>
                            </div>
                        `);
                    });
                }
            });
        });
    </script>
</x-app-layout>
