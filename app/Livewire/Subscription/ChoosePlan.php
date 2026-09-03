<?php

namespace App\Livewire\Subscription;

use Livewire\Attributes\Layout;
use Livewire\Component;

use App\Models\Plan;


#[Layout('layouts.app')]
class ChoosePlan extends Component
{
    public $selectedPlanId = null;


    public function selectPlan($planId)
    {
        $this->selectedPlanId = $planId;
    }


    public function continue()
    {
        $this->validate([
            'selectedPlanId' => ['required', 'exists:plans,id'],
        ]);

        $this->redirectRoute('subscription.checkout', $this->selectedPlanId);
    }

    public function skipForNow()
    {
        $this->redirectRoute('dashboard');
    }


    public function render()
    {
        return view('livewire.subscription.choose-plan', [
            'plans' => Plan::with('features')->get(),
        ]);
    }
}
