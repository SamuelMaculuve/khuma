<?php

namespace App\Livewire\Subscription;

use Livewire\Component;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Checkout extends Component
{
    public Plan $plan;


    public function mount($plan)
    {
        $this->plan = Plan::findOrFail($plan);
    }


    public function subscribe()
    {
        // Aqui você liga Stripe, PayPal, etc
        $this->redirectRoute('subscription.success');
    }


    public function render()
    {
        return view('livewire.subscription.checkout');
    }
}
