<?php

namespace App\Livewire\Subscription;

use Livewire\Component;

use App\Models\Plan;


class ChoosePlan extends Component
{
    public $selectedPlanId = null;


    public function selectPlan($planId)
    {
        $this->selectedPlanId = $planId;
    }


    public function continue()
    {
        $this->redirectRoute('subscription.checkout', $this->selectedPlanId);
    }


    public function render()
    {
        return view('livewire.subscription.choose-plan', [
            'plans' => Plan::with('features')->get(),
        ]);
    }
}
