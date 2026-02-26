<?php

namespace App\Livewire\Subscription;

use Livewire\Component;
use App\Models\Plan;
#[Layout('layouts.app')]
class ChoosePlan extends Component
{
    public ?int $selectedPlanId = null;

    public function selectPlan(int $planId): void
    {
        $this->selectedPlanId = $planId;
    }

    public function continue(): void
    {
        $this->validate(['selectedPlanId' => 'required|exists:plans,id']);

        // Vai directo para o confirm (checkout pode ser removido ou mantido como preview)
        $this->redirectRoute('subscription.confirm', ['plan' => $this->selectedPlanId]);
    }

    public function render()
    {
        return view('livewire.subscription.choose-plan', [
            'plans' => Plan::with('features')->get(),
        ]);
    }
}
