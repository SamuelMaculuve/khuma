<?php

namespace App\Livewire\Subscription;

use App\Models\Payment;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $user = Auth::user();
        $subscription = $user->company_id && $user->company
            ? $user->company->subscription()->with(['plan.features', 'plan.prices', 'payments'])->first()
            : $user->subscription()->with(['plan.features', 'plan.prices', 'payments'])->first();

        return view('livewire.subscription.dashboard', [
            'subscription' => $subscription,
            'currentPlan' => $subscription?->plan,
            'plans' => Plan::with(['features', 'prices'])->orderBy('id')->get(),
            'payments' => $subscription
                ? $subscription->payments()->latest()->take(8)->get()
                : Payment::where('user_id', $user->id)->latest()->take(8)->get(),
        ]);
    }
}
