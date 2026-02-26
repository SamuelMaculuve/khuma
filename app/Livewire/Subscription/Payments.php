<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

#[Layout('layouts.app')]
class Payments extends Component
{
    use WithPagination;

    public string $search  = '';
    public string $statusFilter = '';

    public function updatingSearch(): void    { $this->resetPage(); }
    public function updatingStatusFilter(): void { $this->resetPage(); }

    public function render()
    {
        $payments = Payment::query()
            ->with('subscription.plan')
            ->where('user_id', Auth::id())
            ->when($this->search, fn($q) =>
            $q->where('transaction_reference', 'like', "%{$this->search}%")
                ->orWhere('phone', 'like', "%{$this->search}%")
            )
            ->when($this->statusFilter, fn($q) =>
            $q->where('status', $this->statusFilter)
            )
            ->latest()
            ->paginate(10);

        $totals = Payment::where('user_id', Auth::id())
            ->selectRaw("
                COUNT(*) as total_count,
                SUM(CASE WHEN status = 'paid' THEN amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
                SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed_count
            ")
            ->first();

        return view('livewire.subscription.payments', compact('payments', 'totals'));
    }
}
