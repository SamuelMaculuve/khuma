<div class="py-8 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">

    {{-- Page header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Email Marketing</h1>
            <p class="text-sm text-gray-500 mt-1">Crie e envie campanhas de email para os seus leads e clientes.</p>
        </div>
        <button
            wire:click="openCreate"
            class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition-colors"
        >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nova Campanha
        </button>
    </div>

    {{-- Flash messages --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
            {{ session('error') }}
        </div>
    @endif

    {{-- Search --}}
    <div class="mb-4">
        <input
            type="search"
            wire:model.live.debounce.300ms="search"
            placeholder="Pesquisar campanhas..."
            class="w-full sm:w-72 rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
        />
    </div>

    {{-- Campaigns table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assunto</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Dest.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Enviados</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Falhas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criado</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acções</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($campaigns as $campaign)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900 max-w-xs truncate">{{ $campaign->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $campaign->subject }}</td>
                        <td class="px-6 py-4">
                            @php
                                $badge = match($campaign->status) {
                                    'draft'     => 'bg-gray-100 text-gray-700',
                                    'scheduled' => 'bg-blue-100 text-blue-700',
                                    'sending'   => 'bg-yellow-100 text-yellow-700',
                                    'sent'      => 'bg-green-100 text-green-700',
                                    'failed'    => 'bg-red-100 text-red-700',
                                    default     => 'bg-gray-100 text-gray-700',
                                };
                                $labels = [
                                    'draft'     => 'Rascunho',
                                    'scheduled' => 'Agendado',
                                    'sending'   => 'A enviar',
                                    'sent'      => 'Enviado',
                                    'failed'    => 'Falhou',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ $labels[$campaign->status] ?? $campaign->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600 text-right">{{ $campaign->recipients_count }}</td>
                        <td class="px-6 py-4 text-sm text-green-600 font-medium text-right">{{ $campaign->sent_count }}</td>
                        <td class="px-6 py-4 text-sm text-red-500 font-medium text-right">{{ $campaign->failed_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $campaign->created_at->format('d/m/Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($campaign->status === 'draft')
                                    <button
                                        wire:click="openEdit({{ $campaign->id }})"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                    >Editar</button>
                                    <button
                                        wire:click="send({{ $campaign->id }})"
                                        wire:confirm="Tem a certeza que quer enviar esta campanha? Esta acção não pode ser desfeita."
                                        class="text-xs text-green-600 hover:text-green-800 font-medium"
                                    >Enviar</button>
                                    <button
                                        wire:click="delete({{ $campaign->id }})"
                                        wire:confirm="Eliminar esta campanha?"
                                        class="text-xs text-red-500 hover:text-red-700 font-medium"
                                    >Eliminar</button>
                                @else
                                    <button
                                        wire:click="openStats({{ $campaign->id }})"
                                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                    >Ver Stats</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-16 text-center text-gray-400">
                            <svg class="mx-auto w-10 h-10 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <p class="text-sm font-medium">Nenhuma campanha encontrada</p>
                            <p class="text-xs mt-1">Clique em "Nova Campanha" para começar.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($campaigns->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $campaigns->links() }}
            </div>
        @endif
    </div>

    {{-- Quill CSS + JS --}}
    @once
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <style>
            .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e5e7eb; background: #f9fafb; }
            .ql-container.ql-snow { border: none; }
            .ql-editor { min-height: 220px; }
        </style>
    @endonce

    {{-- Create / Edit form slide-over --}}
    @if($showForm)
        <div class="fixed inset-0 z-50 flex" x-data>
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-gray-900/50" wire:click="$set('showForm', false)"></div>
            {{-- Panel --}}
            <div class="relative ml-auto w-full max-w-2xl bg-white h-full flex flex-col shadow-2xl overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ $editingId ? 'Editar Campanha' : 'Nova Campanha' }}
                    </h2>
                    <button wire:click="$set('showForm', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 px-6 py-6 space-y-5">
                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome da campanha <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Ex: Promoção de Abril"
                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Subject --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Assunto do email <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="subject" placeholder="Ex: Oferta especial para si"
                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                        @error('subject') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Body HTML - Quill rich text editor --}}
                    <div x-data="{
                        quill: null,
                        init() {
                            this.quill = new Quill(this.$refs.editor, {
                                theme: 'snow',
                                modules: {
                                    toolbar: {
                                        container: [
                                            [{ header: [1, 2, 3, false] }],
                                            ['bold', 'italic', 'underline', 'strike'],
                                            [{ color: [] }, { background: [] }],
                                            [{ list: 'ordered' }, { list: 'bullet' }],
                                            [{ align: [] }],
                                            ['blockquote'],
                                            ['link', 'image'],
                                            ['clean']
                                        ],
                                        handlers: {
                                            image: () => {
                                                const input = document.createElement('input');
                                                input.setAttribute('type', 'file');
                                                input.setAttribute('accept', 'image/*');
                                                input.click();
                                                input.onchange = () => {
                                                    const file = input.files[0];
                                                    if (!file) return;
                                                    const reader = new FileReader();
                                                    reader.onload = (e) => {
                                                        const range = this.quill.getSelection(true);
                                                        this.quill.insertEmbed(range.index, 'image', e.target.result);
                                                        this.quill.setSelection(range.index + 1);
                                                    };
                                                    reader.readAsDataURL(file);
                                                };
                                            }
                                        }
                                    }
                                }
                            });

                            const initial = @js($bodyHtml);
                            if (initial) this.quill.root.innerHTML = initial;

                            this.quill.on('text-change', () => {
                                $wire.set('bodyHtml', this.quill.root.innerHTML);
                            });

                            // Sync when form opens with existing content (edit mode)
                            $wire.on('quill-set-content', (html) => {
                                this.quill.root.innerHTML = html || '';
                            });
                        }
                    }">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Corpo do email <span class="text-red-500">*</span>
                        </label>
                        <div class="rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                            <div x-ref="editor" style="min-height: 220px; font-size: 14px;"></div>
                        </div>
                        @error('bodyHtml') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Lead status filters --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar audiência por estado do lead <span class="text-gray-400 text-xs">(deixar vazio = todos os clientes com email)</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach(['new' => 'Novo', 'contacted' => 'Contactado', 'qualified' => 'Qualificado', 'proposal' => 'Proposta', 'negotiation' => 'Negociação', 'won' => 'Ganho', 'lost' => 'Perdido'] as $value => $label)
                                <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                    <input type="checkbox" wire:model="filters.lead_status" value="{{ $value }}"
                                           class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3">
                    <button wire:click="$set('showForm', false)"
                            class="px-4 py-2 text-sm text-gray-700 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">
                        Guardar rascunho
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats modal --}}
    @if($showStats && $statsData)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-900/50" wire:click="closeStats"></div>
            <div class="relative bg-white rounded-xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Estatísticas — {{ $statsData->name }}</h2>
                    <button wire:click="closeStats" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5 space-y-5 overflow-y-auto">
                    {{-- Summary cards --}}
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-gray-900">{{ $statsData->recipients_count }}</p>
                            <p class="text-xs text-gray-500 mt-1">Destinatários</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-green-700">{{ $statsData->sent_count }}</p>
                            <p class="text-xs text-gray-500 mt-1">Enviados</p>
                        </div>
                        <div class="bg-red-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-red-600">{{ $statsData->failed_count }}</p>
                            <p class="text-xs text-gray-500 mt-1">Falhas</p>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    @if($statsData->recipients_count > 0)
                        @php $pct = round(($statsData->sent_count / $statsData->recipients_count) * 100); @endphp
                        <div>
                            <div class="flex justify-between text-xs text-gray-500 mb-1">
                                <span>Progresso</span><span>{{ $pct }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Logs table --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead>
                                <tr class="text-xs text-gray-500 uppercase">
                                    <th class="py-2 pr-4 text-left">Email</th>
                                    <th class="py-2 pr-4 text-left">Estado</th>
                                    <th class="py-2 text-left">Data</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($statsData->logs as $log)
                                    <tr>
                                        <td class="py-2 pr-4 text-gray-700 truncate max-w-xs">{{ $log->email_address }}</td>
                                        <td class="py-2 pr-4">
                                            @if($log->status === 'sent')
                                                <span class="text-green-600 font-medium">Enviado</span>
                                            @elseif($log->status === 'failed')
                                                <span class="text-red-500 font-medium" title="{{ $log->error_message }}">Falhou</span>
                                            @else
                                                <span class="text-gray-400">Pendente</span>
                                            @endif
                                        </td>
                                        <td class="py-2 text-gray-500">{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
