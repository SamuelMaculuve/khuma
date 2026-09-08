<?php

namespace App\Livewire;

use App\Models\Clients;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class ClientList extends Component
{
    use WithPagination;

    public $search = '';

    public ?int   $editingClientId    = null;
    public string $editingClientEmail = '';

    public bool   $showCreateClient   = false;
    public string $new_name           = '';
    public string $new_email          = '';
    public string $new_phone          = '';
    public string $new_address        = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateClient(): void
    {
        $this->reset(['new_name', 'new_email', 'new_phone', 'new_address']);
        $this->resetValidation();
        $this->showCreateClient = true;
    }

    public function closeCreateClient(): void
    {
        $this->showCreateClient = false;
        $this->reset(['new_name', 'new_email', 'new_phone', 'new_address']);
        $this->resetValidation();
    }

    public function createClient(): void
    {
        $data = $this->validate([
            'new_name'    => ['required', 'string', 'max:255'],
            'new_email'   => ['nullable', 'email', 'max:255'],
            'new_phone'   => ['nullable', 'string', 'max:50'],
            'new_address' => ['nullable', 'string', 'max:500'],
        ]);

        Clients::create([
            'company_id' => auth()->user()->company_id,
            'name'       => $data['new_name'],
            'email'      => $data['new_email'] ?: null,
            'phone'      => $data['new_phone'] ?: null,
            'address'    => $data['new_address'] ?: null,
        ]);

        $this->closeCreateClient();
        $this->resetPage();
        $this->dispatch('client-created');
    }

    public function startEditEmail(int $clientId): void
    {
        $client = Clients::where('company_id', auth()->user()->company_id)
            ->findOrFail($clientId);

        $this->editingClientId    = $clientId;
        $this->editingClientEmail = $client->email ?? '';
    }

    public function saveEmail(): void
    {
        $this->validate([
            'editingClientEmail' => 'nullable|email|max:255',
        ]);

        Clients::where('company_id', auth()->user()->company_id)
            ->findOrFail($this->editingClientId)
            ->update(['email' => $this->editingClientEmail ?: null]);

        $this->editingClientId    = null;
        $this->editingClientEmail = '';
    }

    public function cancelEditEmail(): void
    {
        $this->editingClientId    = null;
        $this->editingClientEmail = '';
        $this->resetErrorBag('editingClientEmail');
    }

    public function render()
    {
        $company = auth()->user()->company_id;

        $clients = Clients::query()
            ->where(function ($query) {
                $query->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            })->where('company_id', $company)
            ->latest()
            ->paginate(10);

        return view('livewire.client-list', compact('clients'));
    }
}
