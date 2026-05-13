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

    {{-- Flash --}}
    @if(session('success'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 flex items-center gap-2 px-4 py-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
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
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Abertos</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Cliques</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Resp.</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Falhas</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agenda</th>
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
                        <td class="px-6 py-4 text-sm text-blue-600 font-medium text-right">{{ $campaign->opened_count }}</td>
                        <td class="px-6 py-4 text-sm text-purple-600 font-medium text-right">{{ $campaign->clicked_count }}</td>
                        <td class="px-6 py-4 text-sm text-amber-600 font-medium text-right">{{ $campaign->replied_count }}</td>
                        <td class="px-6 py-4 text-sm text-red-500 font-medium text-right">{{ $campaign->failed_count }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $campaign->scheduled_at ? $campaign->scheduled_at->timezone($campaign->timezone ?? config('app.timezone'))->format('d/m/Y H:i') : $campaign->created_at->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($campaign->status === 'draft')
                                    <button wire:click="openEdit({{ $campaign->id }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Editar</button>
                                    <button wire:click="send({{ $campaign->id }})" wire:confirm="Tem a certeza? Esta acção não pode ser desfeita." class="text-xs text-green-600 hover:text-green-800 font-medium">Enviar agora</button>
                                    <button wire:click="delete({{ $campaign->id }})" wire:confirm="Eliminar?" class="text-xs text-red-500 hover:text-red-700 font-medium">Eliminar</button>
                                @elseif($campaign->status === 'scheduled')
                                    <button wire:click="send({{ $campaign->id }})" wire:confirm="Enviar esta campanha agora?" class="text-xs text-green-600 hover:text-green-800 font-medium">Enviar agora</button>
                                    <button wire:click="cancelSchedule({{ $campaign->id }})" wire:confirm="Cancelar o agendamento?" class="text-xs text-red-500 hover:text-red-700 font-medium">Cancelar agenda</button>
                                    <button wire:click="openStats({{ $campaign->id }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver Stats</button>
                                @else
                                    <button wire:click="openStats({{ $campaign->id }})" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Ver Stats</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="px-6 py-16 text-center text-gray-400">
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

    {{-- Quill --}}
    @once
        <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
        <style>
            .ql-toolbar.ql-snow { border: none; border-bottom: 1px solid #e5e7eb; background: #f9fafb; border-radius: 8px 8px 0 0; }
            .ql-container.ql-snow { border: none; }
            .ql-editor { min-height: 360px; font-size: 14px; }
            .preview-frame { background: #eef2f7; padding: 16px; border-radius: 8px; }
            .preview-frame .preview-card { background: #fff; max-width: 640px; margin: 0 auto; border-radius: 0; overflow: hidden; box-shadow: none; border: 1px solid #e2e8f0; }
            .preview-frame .preview-card img { max-width: 100%; height: auto; display: block; }
            .preview-frame .preview-card p { margin-bottom: 14px; }
            .preview-frame .preview-card a { color: #245f95; }
            .template-email-preview { width: 640px; max-width: 640px; background: #fff; border: 1px solid #e2e8f0; transform: scale(.34); transform-origin: top left; pointer-events: none; }
            .template-email-preview-body { width: 188px; height: 150px; overflow: hidden; }
            .template-email-preview img { max-width: 100%; height: auto; display: block; }
        </style>
    @endonce

    {{-- Wizard --}}
    @if($showWizard)
        <div class="fixed inset-0 z-50 flex" x-data="{}">
            <div class="fixed inset-0 bg-gray-900/60" wire:click="$set('showWizard', false)"></div>
            <div class="relative ml-auto w-full max-w-6xl bg-white h-full flex flex-col shadow-2xl">

                {{-- Header + stepper --}}
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">
                            {{ $editingId ? 'Editar Campanha' : 'Nova Campanha' }}
                        </h2>
                        <p class="text-xs text-gray-500 mt-0.5">Passo {{ $step }} de 3</p>
                    </div>
                    <div class="hidden md:flex items-center gap-2">
                        @foreach(['Modelo', 'Conteúdo', 'Audiência'] as $i => $label)
                            @php $n = $i + 1; @endphp
                            <div class="flex items-center gap-2">
                                <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold
                                    {{ $step >= $n ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-500' }}">{{ $n }}</span>
                                <span class="text-sm {{ $step === $n ? 'text-gray-900 font-medium' : 'text-gray-500' }}">{{ $label }}</span>
                                @if($n < 3) <span class="w-8 h-px bg-gray-200"></span> @endif
                            </div>
                        @endforeach
                    </div>
                    <button wire:click="$set('showWizard', false)" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto">

                    {{-- STEP 1 — Template grid --}}
                    @if($step === 1)
                        <div class="p-6">
                            <div class="mb-6 flex items-end justify-between gap-4 flex-wrap">
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Escolha um modelo</h3>
                                    <p class="text-sm text-gray-500 mt-1">Comece a partir de um modelo pré-desenhado ou crie do zero.</p>
                                </div>
                                <button wire:click="startBlank" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">Começar em branco →</button>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                                @foreach($templates as $tpl)
                                    <button wire:click="pickTemplate({{ $tpl->id }})"
                                            class="group text-left bg-white rounded-xl border border-gray-200 hover:border-indigo-400 hover:shadow-md transition-all overflow-hidden">
                                        <div class="aspect-[4/3] bg-slate-100 border-b border-gray-100 overflow-hidden p-3 flex items-start justify-center">
                                            <div class="template-email-preview-body">
                                                <div class="template-email-preview">
                                                    <div style="padding:22px 28px 14px;border-bottom:3px solid #245f95">
                                                        <div style="font-size:20px;font-weight:700;color:#0f172a;line-height:1.2;">{{ auth()->user()->company?->name ?? 'A sua empresa' }}</div>
                                                        <div style="font-size:12px;color:#64748b;margin-top:6px;">{{ $tpl->name }}</div>
                                                    </div>
                                                    <div style="padding:28px;color:#273241;font-family:Arial,Helvetica,sans-serif;font-size:15px;line-height:1.65;">
                                                        {!! $this->templatePreviewHtml($tpl) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="p-4">
                                            <div class="flex items-center justify-between">
                                                <h4 class="text-sm font-semibold text-gray-900 group-hover:text-indigo-600">{{ $tpl->name }}</h4>
                                                <span class="text-[10px] uppercase tracking-wider text-gray-400">{{ $tpl->category }}</span>
                                            </div>
                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $tpl->subject }}</p>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- STEP 2 — Content (editor + preview + image manager) --}}
                    @if($step === 2)
                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-0 h-full">
                            {{-- Left: editor + meta --}}
                            <div class="lg:col-span-3 p-6 border-r border-gray-200 space-y-5 overflow-y-auto">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Nome da campanha <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="name" placeholder="Ex: Promoção de Maio"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Assunto <span class="text-red-500">*</span></label>
                                    <input type="text" wire:model="subject" placeholder="Assunto do email"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('subject') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Pré-cabeçalho</label>
                                    <input type="text" wire:model="preheader" maxlength="180" placeholder="Texto curto que aparece antes de abrir o email"
                                           class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                    @error('preheader') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nome do remetente</label>
                                        <input type="text" wire:model="senderName" placeholder="{{ auth()->user()->company?->name ?? 'A sua empresa' }}"
                                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('senderName') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email do remetente</label>
                                        <input type="email" wire:model="senderEmail" placeholder="{{ config('mail.from.address') }}"
                                               class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                        @error('senderEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Respostas para equipa</label>
                                        <select wire:model="replyTeamId" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                            <option value="">Inbox padrão de campanhas</option>
                                            @foreach($this->replyTeamOptions as $team)
                                                <option value="{{ $team->id }}">
                                                    {{ $team->name }}{{ $team->emailAddress() ? ' · ' . $team->emailAddress() : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('replyTeamId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        @if($this->replyTeamOptions->isEmpty())
                                            <p class="mt-1 text-xs text-gray-500">Crie equipas em Configurações > Equipas para encaminhar respostas.</p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Image manager (placeholder slots from template) --}}
                                @if(!empty($images))
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Imagens do modelo</label>
                                        <div class="space-y-2">
                                            @foreach($images as $key => $url)
                                                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                                    <div class="w-16 h-12 bg-white rounded border border-gray-200 flex items-center justify-center overflow-hidden flex-shrink-0">
                                                        @if($url)
                                                            <img src="{{ $url }}" alt="" class="w-full h-full object-cover" />
                                                        @else
                                                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                        @endif
                                                    </div>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-sm font-medium text-gray-700">{{ ucfirst(str_replace('_', ' ', $key)) }}</p>
                                                        <p class="text-xs text-gray-400 truncate">{{ $url ?: 'Sem imagem — clique para adicionar' }}</p>
                                                    </div>
                                                    <button type="button" wire:click="selectPlaceholderForUpload('{{ $key }}')" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">{{ $url ? 'Trocar' : 'Adicionar' }}</button>
                                                    @if($url)
                                                        <button type="button" wire:click="clearPlaceholderImage('{{ $key }}')" class="text-xs text-red-500 hover:text-red-700">Remover</button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                {{-- Editor --}}
                                <div x-data="{
                                    quill: null,
                                    applyingRemoteContent: false,
                                    init() {
                                        if (this.$refs.editor.__quill) {
                                            this.quill = this.$refs.editor.__quill;
                                            return;
                                        }

                                        this.$refs.editor.innerHTML = '';
                                        this.$refs.editor.parentElement.querySelectorAll(':scope > .ql-toolbar').forEach((toolbar) => toolbar.remove());

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
                                                        ['blockquote', 'link', 'image'],
                                                        ['clean']
                                                    ],
                                                    handlers: {
                                                        image: () => {
                                                            $wire.set('uploadingPlaceholder', null);
                                                            this.$refs.fileInput.click();
                                                        }
                                                    }
                                                }
                                            }
                                        });
                                        this.$refs.editor.__quill = this.quill;

                                        const initial = @js($bodyHtml);
                                        if (initial) this.quill.root.innerHTML = initial;

                                        this.quill.on('text-change', () => {
                                            if (this.applyingRemoteContent) return;
                                            $wire.set('bodyHtml', this.quill.root.innerHTML, false);
                                        });

                                        $wire.on('quill-set-content', (e) => {
                                            const html = (e && e.html) || (Array.isArray(e) ? e[0]?.html : '') || '';
                                            this.applyingRemoteContent = true;
                                            this.quill.root.innerHTML = html;
                                            this.quill.update('silent');
                                            setTimeout(() => {
                                                this.applyingRemoteContent = false;
                                            }, 0);
                                        });

                                        $wire.on('quill-insert-image', (e) => {
                                            const url = (e && e.url) || (Array.isArray(e) ? e[0]?.url : '');
                                            if (!url) return;
                                            const range = this.quill.getSelection(true);
                                            this.quill.insertEmbed(range.index, 'image', url);
                                            this.quill.setSelection(range.index + 1);
                                        });

                                        $wire.on('trigger-image-upload', () => {
                                            this.$refs.fileInput.click();
                                        });
                                    }
                                }">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Corpo do email <span class="text-red-500">*</span></label>
                                    <p class="text-xs text-gray-500 mb-2">Use <code class="bg-gray-100 px-1 rounded">@{{name}}</code>, <code class="bg-gray-100 px-1 rounded">@{{email}}</code>, <code class="bg-gray-100 px-1 rounded">@{{company_name}}</code> para personalizar.</p>
                                    <div wire:ignore class="rounded-lg border border-gray-300 overflow-hidden focus-within:ring-2 focus-within:ring-indigo-500">
                                        <div x-ref="editor"></div>
                                    </div>
                                    <input type="file" x-ref="fileInput" wire:model="imageUpload" accept="image/*" class="hidden" />
                                    @error('bodyHtml') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    @error('imageUpload') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                    <div wire:loading wire:target="imageUpload" class="text-xs text-indigo-600 mt-2">A enviar imagem…</div>
                                </div>
                            </div>

                            {{-- Right: live preview --}}
                            <div class="lg:col-span-2 bg-gray-50 p-6 overflow-y-auto">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="text-sm font-semibold text-gray-700">Pré-visualização</h4>
                                    <span class="text-[10px] text-gray-400 uppercase tracking-wider">amostra</span>
                                </div>
                                <div class="preview-frame">
                                    <div class="preview-card">
                                        <div style="background:#ffffff;padding:24px 28px 16px;border-bottom:3px solid #245f95">
                                            <div style="font-weight:700;font-size:18px;color:#0f172a;line-height:1.2">{{ auth()->user()->company?->name ?? 'A sua empresa' }}</div>
                                            <div style="font-size:12px;color:#64748b;margin-top:6px;line-height:1.5">{{ $subject ?: 'Assunto do email' }}</div>
                                        </div>
                                        <div style="padding:28px;color:#273241;font-size:14px;line-height:1.65">
                                            {!! $this->previewHtml !!}
                                        </div>
                                        <div style="background:#f8fafc;padding:16px 28px;border-top:1px solid #e2e8f0;font-size:11px;color:#94a3b8;line-height:1.6">
                                            {{ auth()->user()->company?->name ?? 'A sua empresa' }}<br>
                                            Enviado via Khuma CRM
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- STEP 3 — Audience --}}
                    @if($step === 3)
                        <div class="p-6 max-w-3xl mx-auto space-y-6">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900">Audiência</h3>
                                <p class="text-sm text-gray-500 mt-1">Escolha quem vai receber esta campanha.</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-3">Filtrar por estado do lead</p>
                                <p class="text-xs text-gray-500 mb-3">Deixe vazio para enviar a todos os clientes com email.</p>
                                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                    @foreach(['new' => 'Novo', 'contacted' => 'Contactado', 'qualified' => 'Qualificado', 'proposal' => 'Proposta', 'negotiation' => 'Negociação', 'won' => 'Ganho', 'lost' => 'Perdido'] as $value => $label)
                                        <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer p-2 rounded hover:bg-white">
                                            <input type="checkbox" wire:model.live="filters.lead_status" value="{{ $value }}"
                                                   class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                            {{ $label }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="flex items-center gap-3 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-5.13a4 4 0 11-8 0 4 4 0 018 0zm6 3a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <div>
                                    <p class="text-sm font-semibold text-indigo-900">{{ $this->recipientCount }} destinatário(s) correspondem</p>
                                    <p class="text-xs text-indigo-700">Clientes com email válido na sua empresa.</p>
                                </div>
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-gray-200 space-y-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Envio</p>
                                    <p class="text-xs text-gray-500 mt-1">Escolha se esta campanha sai agora ou fica agendada.</p>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:border-indigo-300">
                                        <input type="radio" wire:model.live="sendMode" value="now" class="text-indigo-600 focus:ring-indigo-500" />
                                        <span>
                                            <span class="block text-sm font-medium text-gray-900">Enviar agora</span>
                                            <span class="block text-xs text-gray-500">Guarda e coloca na fila imediatamente.</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-3 rounded-lg border border-gray-200 p-3 cursor-pointer hover:border-indigo-300">
                                        <input type="radio" wire:model.live="sendMode" value="scheduled" class="text-indigo-600 focus:ring-indigo-500" />
                                        <span>
                                            <span class="block text-sm font-medium text-gray-900">Agendar</span>
                                            <span class="block text-xs text-gray-500">Define dia, hora e fuso horário.</span>
                                        </span>
                                    </label>
                                </div>
                                @if($sendMode === 'scheduled')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Data e hora</label>
                                            <input type="datetime-local" wire:model="scheduledAt"
                                                   class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500" />
                                            @error('scheduledAt') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Fuso horário</label>
                                            <select wire:model="timezone" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                @foreach($this->timezoneOptions as $tz)
                                                    <option value="{{ $tz }}">{{ $tz }}</option>
                                                @endforeach
                                            </select>
                                            @error('timezone') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="bg-white rounded-xl p-5 border border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-1">Quando alguém responder</p>
                                <p class="text-xs text-gray-500 mb-3">Controle se respostas viram leads ou ficam apenas no histórico da campanha.</p>
                                <select wire:model="replyAction" class="w-full rounded-lg border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @foreach($this->replyActionOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('replyAction') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Footer actions --}}
                <div class="px-6 py-4 border-t border-gray-200 bg-white flex items-center justify-between">
                    <div>
                        @if($step > 1)
                            <button wire:click="prevStep" class="px-4 py-2 text-sm text-gray-700 hover:text-gray-900">← Voltar</button>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                        <button wire:click="$set('showWizard', false)" class="px-4 py-2 text-sm text-gray-600 hover:text-gray-900">Cancelar</button>
                        @if($step < 3)
                            @if($step >= 2)
                                <button wire:click="save" class="px-4 py-2 text-sm font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">Guardar rascunho</button>
                            @endif
                            <button wire:click="nextStep" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">Continuar →</button>
                        @else
                            <button wire:click="save" class="px-4 py-2 text-sm font-medium text-indigo-600 border border-indigo-200 rounded-lg hover:bg-indigo-50">Guardar rascunho</button>
                            <button wire:click="saveAndQueue" class="px-5 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700">
                                {{ $sendMode === 'scheduled' ? 'Agendar campanha' : 'Enviar campanha' }}
                            </button>
                        @endif
                    </div>
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
                    <div class="grid grid-cols-2 sm:grid-cols-6 gap-4">
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
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-blue-700">{{ $statsData->logs->whereNotNull('opened_at')->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Abertos</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-purple-700">{{ $statsData->logs->whereNotNull('clicked_at')->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Cliques</p>
                        </div>
                        <div class="bg-amber-50 rounded-lg p-4 text-center">
                            <p class="text-2xl font-bold text-amber-700">{{ $statsData->logs->whereNotNull('replied_at')->count() }}</p>
                            <p class="text-xs text-gray-500 mt-1">Respostas</p>
                        </div>
                    </div>
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
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm divide-y divide-gray-100">
                            <thead>
                                <tr class="text-xs text-gray-500 uppercase">
                                    <th class="py-2 pr-4 text-left">Email</th>
                                    <th class="py-2 pr-4 text-left">Estado</th>
                                    <th class="py-2 pr-4 text-left">Engajamento</th>
                                    <th class="py-2 text-left">Lead</th>
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
                                        <td class="py-2 pr-4 text-gray-500">
                                            <div>Enviado: {{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                            <div>Aberto: {{ $log->opened_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                            <div>Clique: {{ $log->clicked_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                            <div>Resposta: {{ $log->replied_at?->format('d/m/Y H:i') ?? '—' }}</div>
                                        </td>
                                        <td class="py-2 text-gray-500">
                                            @if($log->lead)
                                                <span class="text-indigo-600 font-medium">{{ $log->lead->reference }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
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
