<div>
    {{-- Sub-nav tabs --}}
    <div style="height:44px;background:#fff;border-bottom:1px solid #d9d9d9;display:flex;align-items:center;padding:0 16px;gap:4px;">
        <button wire:click="showKanban"
                style="height:34px;padding:0 14px;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;border:none;transition:background .15s;
                       background:{{ $view === 'kanban' ? '#2c6fad' : 'transparent' }};
                       color:{{ $view === 'kanban' ? '#fff' : '#555' }};">
            Todos os Leads
        </button>
        <button wire:click="showClientes"
                style="height:34px;padding:0 14px;border-radius:4px;font-size:13px;font-weight:500;cursor:pointer;border:none;transition:background .15s;
                       background:{{ $view === 'clientes' ? '#2c6fad' : 'transparent' }};
                       color:{{ $view === 'clientes' ? '#fff' : '#555' }};">
            Clientes
        </button>
    </div>

    @if($view === 'kanban')
        <livewire:kanban-board />
    @endif

    @if($view === 'clientes')
        <livewire:client-list />
    @endif
</div>
