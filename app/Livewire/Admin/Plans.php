<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use App\Models\PlanFeature;
use App\Models\PlanPrice;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Plans extends Component
{
    public ?int $selectedPlanId = null;
    public string $name = '';
    public string $description = '';
    public string $amount = '';
    public string $currency = 'MZN';
    public array $features = [];

    public array $featureCatalog = [
        'crm' => ['label' => 'CRM e pipeline', 'type' => 'boolean'],
        'whatsapp' => ['label' => 'WhatsApp', 'type' => 'boolean'],
        'email_campaigns' => ['label' => 'Email marketing', 'type' => 'boolean'],
        'reports' => ['label' => 'Relatórios', 'type' => 'boolean'],
        'settings' => ['label' => 'Configurações', 'type' => 'boolean'],
        'team_management' => ['label' => 'Gestão de equipa', 'type' => 'boolean'],
        'call_logs' => ['label' => 'Registos de chamadas', 'type' => 'boolean'],
        'product_sales' => ['label' => 'Venda de produtos', 'type' => 'boolean'],
        'bulk_messages' => ['label' => 'Mensagens em massa', 'type' => 'boolean'],
        'whatsapp_templates' => ['label' => 'Templates WhatsApp', 'type' => 'boolean'],
        'priority_support' => ['label' => 'Suporte prioritário', 'type' => 'boolean'],
        'members' => ['label' => 'Limite de membros', 'type' => 'limit'],
        'chatbot_lines' => ['label' => 'Linhas no chatbot', 'type' => 'limit'],
        'whatsapp_instances' => ['label' => 'Instâncias WhatsApp', 'type' => 'limit'],
    ];

    public function mount(): void
    {
        $this->selectPlan(Plan::orderBy('id')->value('id'));
    }

    public function selectPlan(?int $planId): void
    {
        if (!$planId) {
            return;
        }

        $plan = Plan::with(['features', 'prices'])->findOrFail($planId);
        $price = $plan->currentPrice();

        $this->selectedPlanId = $plan->id;
        $this->name = $plan->name;
        $this->description = $plan->description ?? '';
        $this->amount = (string) ($price?->amount ?? '');
        $this->currency = $price?->currency ?? 'MZN';
        $this->features = [];

        foreach ($this->featureCatalog as $key => $meta) {
            $value = $plan->feature($key);

            $this->features[$key] = [
                'enabled' => $plan->hasFeature($key),
                'value' => $meta['type'] === 'limit' ? (string) ($value ?: '') : '1',
            ];
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $plan = Plan::findOrFail($this->selectedPlanId);
        $plan->update([
            'name' => $this->name,
            'description' => $this->description ?: null,
        ]);

        PlanPrice::where('plan_id', $plan->id)->update(['is_active' => false]);
        PlanPrice::create([
            'plan_id' => $plan->id,
            'amount' => $this->amount,
            'currency' => strtoupper($this->currency),
            'is_active' => true,
            'starts_at' => now(),
        ]);

        foreach ($this->featureCatalog as $key => $meta) {
            $enabled = (bool) ($this->features[$key]['enabled'] ?? false);
            $value = $meta['type'] === 'limit'
                ? trim((string) ($this->features[$key]['value'] ?? ''))
                : '1';

            PlanFeature::updateOrCreate(
                ['plan_id' => $plan->id, 'feature_key' => $key],
                ['feature_value' => $enabled ? ($value !== '' ? $value : 'unlimited') : '0']
            );
        }

        session()->flash('success', 'Plano atualizado com sucesso.');
        $this->selectPlan($plan->id);
    }

    public function render()
    {
        return view('livewire.admin.plans', [
            'plans' => Plan::with(['features', 'prices'])->orderBy('id')->get(),
        ]);
    }
}
