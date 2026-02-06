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

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
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
