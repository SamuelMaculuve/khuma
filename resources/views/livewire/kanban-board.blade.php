<div>
<style>
    .kb-board { display:flex; gap:12px; overflow-x:auto; padding:16px; min-height:calc(100vh - 100px); align-items:flex-start; }
    .kb-col { flex-shrink:0; width:272px; display:flex; flex-direction:column; gap:0; }
    .kb-col-head { padding:10px 12px; border-radius:6px 6px 0 0; display:flex; align-items:center; justify-content:space-between; }
    .kb-col-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; }
    .kb-col-count { font-size:11px; font-weight:600; background:rgba(0,0,0,.12); padding:1px 7px; border-radius:10px; }
    .kb-col-body { background:#f0f0f0; border-radius:0 0 6px 6px; padding:8px; min-height:120px; display:flex; flex-direction:column; gap:6px; transition:background .15s; }
    .kb-col-body.drag-over { background:#dbeafe; outline:2px dashed #2c6fad; }
    .kb-card { background:#fff; border-radius:6px; padding:10px 12px; cursor:grab; box-shadow:0 1px 3px rgba(0,0,0,.08); border:1px solid #e8e8e8; transition:box-shadow .15s, transform .1s; user-select:none; }
    .kb-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.12); transform:translateY(-1px); }
    .kb-card.dragging { opacity:.45; box-shadow:0 8px 24px rgba(0,0,0,.18); cursor:grabbing; }
    .kb-card-title { font-size:13px; font-weight:600; color:#1a1a1a; line-height:1.35; margin-bottom:6px; }
    .kb-card-client { font-size:11px; color:#666; display:flex; align-items:center; gap:5px; margin-bottom:6px; }
    .kb-card-avatar { width:18px; height:18px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:9px; font-weight:700; color:#fff; flex-shrink:0; }
    .kb-card-footer { display:flex; align-items:center; justify-content:space-between; margin-top:8px; padding-top:8px; border-top:1px solid #f0f0f0; }
    .kb-card-value { font-size:11px; font-weight:700; color:#2c6fad; }
    .kb-card-date { font-size:10px; color:#aaa; }
    .kb-card-badge { font-size:10px; padding:1px 6px; border-radius:10px; font-weight:600; }
    .kb-empty { text-align:center; padding:20px 8px; color:#bbb; font-size:12px; border:1.5px dashed #d9d9d9; border-radius:6px; }
    .kb-add-btn { width:100%; margin-top:4px; padding:7px; border:1.5px dashed #ccc; border-radius:6px; background:transparent; color:#888; font-size:12px; cursor:pointer; transition:all .15s; }
    .kb-add-btn:hover { border-color:#2c6fad; color:#2c6fad; background:#f0f6ff; }
    .kb-toolbar { display:flex; align-items:center; gap:8px; padding:10px 16px; background:#fff; border-bottom:1px solid #e8e8e8; }
    .kb-search { flex:1; max-width:240px; height:32px; padding:0 10px; border:1px solid #d9d9d9; border-radius:4px; font-size:13px; outline:none; }
    .kb-search:focus { border-color:#2c6fad; }
</style>

{{-- Toolbar --}}
<div class="kb-toolbar">
    <input class="kb-search" type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar leads...">
    <select wire:model.live="selectedTeamId" style="height:32px;border:1px solid #d9d9d9;border-radius:4px;font-size:13px;padding:0 10px;background:#fff;color:#444;">
        <option value="all">Todas as equipas</option>
        @foreach($availableTeams as $team)
            <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
        @endforeach
    </select>
    <span style="font-size:12px;color:#888;margin-left:auto;">
        {{ array_sum(array_map('count', $states)) }} leads
    </span>
</div>

{{-- Board --}}
<div class="kb-board" id="kb-board">
    @php
        $colConfig = [
            'new'         => ['label'=>'Novo',        'hbg'=>'#64748b','hfg'=>'#fff'],
            'contacted'   => ['label'=>'Contactado',  'hbg'=>'#3b82f6','hfg'=>'#fff'],
            'qualified'   => ['label'=>'Qualificado', 'hbg'=>'#0891b2','hfg'=>'#fff'],
            'proposal'    => ['label'=>'Proposta',    'hbg'=>'#7c3aed','hfg'=>'#fff'],
            'negotiation' => ['label'=>'Negociação',  'hbg'=>'#d97706','hfg'=>'#fff'],
            'won'         => ['label'=>'Ganho',       'hbg'=>'#16a34a','hfg'=>'#fff'],
            'lost'        => ['label'=>'Perdido',     'hbg'=>'#dc2626','hfg'=>'#fff'],
        ];
        $avatarColors = ['#6366f1','#0891b2','#d97706','#16a34a','#dc2626','#7c3aed','#0d9488'];
    @endphp

    @foreach($states as $stateName => $items)
        @php $cfg = $colConfig[$stateName] ?? ['label'=>$stateName,'hbg'=>'#888','hfg'=>'#fff']; @endphp

        <div class="kb-col" wire:key="col-{{ $stateName }}">
            {{-- Column header --}}
            <div class="kb-col-head" style="background:{{ $cfg['hbg'] }};color:{{ $cfg['hfg'] }};">
                <span class="kb-col-title">{{ $cfg['label'] }}</span>
                <div style="display:flex;align-items:center;gap:6px;">
                    <span class="kb-col-count">{{ count($items) }}</span>
                    <button onclick="openLeadForm('{{ $stateName }}')"
                            style="width:20px;height:20px;border-radius:50%;background:rgba(255,255,255,.25);border:none;cursor:pointer;color:{{ $cfg['hfg'] }};display:flex;align-items:center;justify-content:center;font-size:14px;line-height:1;"
                            title="Adicionar lead">+</button>
                </div>
            </div>

            {{-- Drop zone --}}
            <div class="kb-col-body"
                 id="zone-{{ $stateName }}"
                 data-state="{{ $stateName }}"
                 ondragover="kbDragOver(event)"
                 ondragleave="kbDragLeave(event)"
                 ondrop="kbDrop(event, '{{ $stateName }}')">

                @forelse($items as $item)
                    @php
                        $clientName = $item['client_name'] ?? '';
                        $initials   = strtoupper(substr($clientName ?: $item['title'], 0, 1));
                        $avatarBg   = $avatarColors[$item['id'] % count($avatarColors)];
                        $searchLower = strtolower($search ?? '');
                        $hidden = $searchLower && !str_contains(strtolower($item['title']), $searchLower)
                                               && !str_contains(strtolower($clientName), $searchLower);
                    @endphp
                    @if(!$hidden)
                    <div class="kb-card"
                         wire:key="card-{{ $item['id'] }}"
                         id="card-{{ $item['id'] }}"
                         draggable="true"
                         data-id="{{ $item['id'] }}"
                         data-state="{{ $stateName }}"
                         ondragstart="kbDragStart(event, {{ $item['id'] }}, '{{ $stateName }}')">

                        {{-- Title --}}
                        <a href="{{ route('lead.show', $item['id']) }}" style="text-decoration:none;">
                            <p class="kb-card-title">{{ $item['title'] }}</p>
                        </a>

                        {{-- Client --}}
                        @if($clientName)
                            <div class="kb-card-client">
                                <div class="kb-card-avatar" style="background:{{ $avatarBg }}">{{ $initials }}</div>
                                <span>{{ $clientName }}</span>
                            </div>
                        @endif

                        {{-- Source badge --}}
                        @if(!empty($item['source']))
                            <div style="margin-bottom:4px;">
                                <span class="kb-card-badge" style="background:#f0f0f0;color:#555;">{{ $item['source'] }}</span>
                            </div>
                        @endif
                        @if(!empty($item['team_name']))
                            <div style="margin-bottom:4px;">
                                <span class="kb-card-badge" style="background:#dbeafe;color:#1d4ed8;">{{ $item['team_name'] }}</span>
                            </div>
                        @endif

                        {{-- Footer --}}
                        <div class="kb-card-footer">
                            @if(!empty($item['value']))
                                <span class="kb-card-value">{{ number_format($item['value'], 0) }} MZN</span>
                            @else
                                <span></span>
                            @endif
                            @if(!empty($item['time']))
                                <span class="kb-card-date">{{ $item['time'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endif
                @empty
                    <div class="kb-empty">Sem leads</div>
                @endforelse

                <button class="kb-add-btn" onclick="openLeadForm('{{ $stateName }}')">+ Adicionar lead</button>
            </div>
        </div>
    @endforeach
</div>

{{-- Lead form modal --}}
@if($showLeadForm)
<div style="position:fixed;inset:0;z-index:999;background:rgba(0,0,0,.45);display:flex;align-items:center;justify-content:center;padding:16px;"
     wire:click.self="closeLeadForm">
    <div style="background:#fff;border-radius:8px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.2);">
        <div style="padding:16px 20px;border-bottom:1px solid #e8e8e8;display:flex;align-items:center;justify-content:space-between;">
            <div>
                <p style="font-size:15px;font-weight:700;color:#333;">Novo Lead</p>
                @php $cfg2 = $colConfig[$leadStatus] ?? ['label'=>$leadStatus,'hbg'=>'#888']; @endphp
                <span style="font-size:11px;font-weight:600;color:{{ $cfg2['hbg'] }};">{{ $cfg2['label'] }}</span>
            </div>
            <button wire:click="closeLeadForm" style="background:none;border:none;cursor:pointer;color:#999;font-size:20px;line-height:1;">&times;</button>
        </div>

        <form wire:submit.prevent="saveLead" style="padding:20px;display:flex;flex-direction:column;gap:14px;">

            {{-- Client selector --}}
            <div>
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
                    <label style="font-size:13px;font-weight:500;color:#444;">Cliente</label>
                    <button type="button" wire:click="toggleNewClient"
                            style="font-size:12px;color:#2c6fad;background:none;border:none;cursor:pointer;font-weight:500;">
                        {{ $creatingNewClient ? '← Usar existente' : '+ Novo cliente' }}
                    </button>
                </div>
                @if($creatingNewClient)
                    <div style="border:1.5px dashed #93c5fd;border-radius:6px;padding:10px;background:#eff6ff;display:flex;flex-direction:column;gap:6px;">
                        <input type="text" wire:model="new_client_name" placeholder="Nome *"
                               style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                        @error('new_client_name')<p style="font-size:11px;color:#dc2626;">{{ $message }}</p>@enderror
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:6px;">
                            <input type="email" wire:model="new_client_email" placeholder="Email"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                            <input type="text" wire:model="new_client_phone" placeholder="Telefone"
                                   style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                        </div>
                    </div>
                @else
                    <select wire:model="lead_client_id"
                            style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                        <option value="">Seleccionar cliente</option>
                        @foreach($availableClients as $c)
                            <option value="{{ $c['id'] }}">{{ $c['name'] }}</option>
                        @endforeach
                    </select>
                    @error('lead_client_id')<p style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</p>@enderror
                @endif
            </div>

            <div>
                <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Título *</label>
                <input type="text" wire:model="lead_title" placeholder="Ex: Proposta de CRM"
                       style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                @error('lead_title')<p style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</p>@enderror
            </div>

            <div>
                <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Descrição</label>
                <textarea wire:model="lead_description" rows="2" placeholder="Detalhes..."
                          style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;resize:vertical;"></textarea>
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <div>
                    <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Valor (MZN)</label>
                    <input type="number" wire:model="lead_value" step="0.01" min="0" placeholder="0.00"
                           style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                </div>
                <div>
                    <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Data prevista</label>
                    <input type="date" wire:model="lead_expected_close_date"
                           style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                </div>
            </div>

            <div>
                <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Fonte</label>
                <input type="text" wire:model="lead_source" placeholder="WhatsApp, website, indicação..."
                       style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
            </div>

            <div>
                <label style="font-size:13px;font-weight:500;color:#444;display:block;margin-bottom:5px;">Equipa / pipeline</label>
                <select wire:model="lead_team_id"
                        style="width:100%;border:1px solid #d1d5db;border-radius:4px;padding:7px 10px;font-size:13px;">
                    <option value="">Sem equipa</option>
                    @foreach($availableTeams as $team)
                        <option value="{{ $team['id'] }}">{{ $team['name'] }}</option>
                    @endforeach
                </select>
                @error('lead_team_id')<p style="font-size:11px;color:#dc2626;margin-top:3px;">{{ $message }}</p>@enderror
            </div>

            <div style="display:flex;justify-content:flex-end;gap:8px;padding-top:4px;">
                <button type="button" wire:click="closeLeadForm"
                        style="padding:8px 16px;border:1px solid #d1d5db;border-radius:4px;background:#fff;font-size:13px;cursor:pointer;">
                    Cancelar
                </button>
                <button type="submit" wire:loading.attr="disabled"
                        style="padding:8px 18px;border:none;border-radius:4px;background:#2c6fad;color:#fff;font-size:13px;font-weight:600;cursor:pointer;">
                    <span wire:loading.remove wire:target="saveLead">Guardar</span>
                    <span wire:loading wire:target="saveLead">A guardar...</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    // ── Drag state ──────────────────────────────────────
    let kbDragId    = null;
    let kbDragFrom  = null;
    let kbDragEl    = null;

    function kbDragStart(event, id, fromState) {
        kbDragId   = id;
        kbDragFrom = fromState;
        kbDragEl   = event.currentTarget;
        event.dataTransfer.effectAllowed = 'move';
        event.dataTransfer.setData('text/plain', String(id));
        setTimeout(() => kbDragEl?.classList.add('dragging'), 0);
    }

    function kbDragOver(event) {
        event.preventDefault();
        event.dataTransfer.dropEffect = 'move';
        const zone = event.currentTarget;
        if (zone.classList.contains('kb-col-body')) {
            zone.classList.add('drag-over');
        }
    }

    function kbDragLeave(event) {
        if (!event.currentTarget.contains(event.relatedTarget)) {
            event.currentTarget.classList.remove('drag-over');
        }
    }

    function kbDrop(event, toState) {
        event.preventDefault();
        event.currentTarget.classList.remove('drag-over');

        if (kbDragEl) kbDragEl.classList.remove('dragging');

        if (kbDragId !== null && kbDragFrom && kbDragFrom !== toState) {
            @this.moveItem(kbDragId, kbDragFrom, toState);
        }

        kbDragId   = null;
        kbDragFrom = null;
        kbDragEl   = null;
    }

    document.addEventListener('dragend', () => {
        document.querySelectorAll('.kb-col-body').forEach(z => z.classList.remove('drag-over'));
        kbDragEl?.classList.remove('dragging');
        kbDragId = kbDragFrom = kbDragEl = null;
    });

    // ── Open lead form from JS ───────────────────────────
    function openLeadForm(stateName) {
        @this.openLeadForm(stateName);
    }
</script>
</div>
