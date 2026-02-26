<?php

namespace App\Livewire\Plans;

use App\Models\Plan;
use App\Models\PlanPrice;
use Livewire\Component;
use Livewire\WithPagination;

class PlanList extends Component
{
    use WithPagination;

    // Modal state
    public bool $showModal = false;
    public bool $showDeleteModal = false;
    public bool $showFeaturesModal = false;

    // Form fields
    public ?int $planId = null;
    public string $code = '';
    public string $name = '';
    public string $description = '';
    public float|string $amount = '';
    public string $currency = 'MZN';

    // Features (array of ['feature_key' => '', 'feature_value' => ''])
    public array $features = [];

    // Delete
    public ?int $deletingId = null;

    // Features modal plan
    public ?int $featuresPlanId = null;

    protected function rules(): array
    {
        return [
            'code'        => 'required|string|max:50|unique:plans,code' . ($this->planId ? ",{$this->planId}" : ''),
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'amount'      => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'features'              => 'array',
            'features.*.feature_key'   => 'required|string|max:100',
            'features.*.feature_value' => 'nullable|string|max:255',
        ];
    }

    protected $messages = [
        'code.required'                    => 'O código é obrigatório.',
        'code.unique'                      => 'Este código já está em uso.',
        'name.required'                    => 'O nome é obrigatório.',
        'amount.required'                  => 'O preço é obrigatório.',
        'amount.numeric'                   => 'O preço deve ser um número.',
        'features.*.feature_key.required'  => 'A chave da funcionalidade é obrigatória.',
    ];

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $plan = Plan::with(['activePrice', 'features'])->findOrFail($id);

        $this->planId      = $plan->id;
        $this->code        = $plan->code;
        $this->name        = $plan->name;
        $this->description = $plan->description ?? '';
        $this->amount      = $plan->activePrice?->amount ?? '';
        $this->currency    = $plan->activePrice?->currency ?? 'MZN';
        $this->features    = $plan->features->map(fn($f) => [
            'feature_key'   => $f->feature_key,
            'feature_value' => $f->feature_value,
        ])->toArray();

        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        $plan = Plan::updateOrCreate(
            ['id' => $this->planId],
            [
                'code'        => $this->code,
                'name'        => $this->name,
                'description' => $this->description ?: null,
            ]
        );

        // Deactivate old prices and create a new active one
        $plan->prices()->update(['is_active' => false]);
        $plan->prices()->create([
            'amount'     => $this->amount,
            'currency'   => $this->currency,
            'is_active'  => true,
            'starts_at'  => now(),
        ]);

        // Sync features
        $plan->features()->delete();
        foreach ($this->features as $feature) {
            if (!empty($feature['feature_key'])) {
                $plan->features()->create($feature);
            }
        }

        $this->resetForm();
        $this->showModal = false;

        session()->flash('success', $this->planId ? 'Plano actualizado com sucesso.' : 'Plano criado com sucesso.');
    }

    public function confirmDelete(int $id): void
    {
        $this->deletingId     = $id;
        $this->showDeleteModal = true;
    }

    public function delete(): void
    {
        if ($this->deletingId) {
            Plan::findOrFail($this->deletingId)->delete();
        }
        $this->deletingId      = null;
        $this->showDeleteModal = false;
        session()->flash('success', 'Plano eliminado com sucesso.');
    }

    public function addFeature(): void
    {
        $this->features[] = ['feature_key' => '', 'feature_value' => ''];
    }

    public function removeFeature(int $index): void
    {
        array_splice($this->features, $index, 1);
        $this->features = array_values($this->features);
    }

    public function closeModal(): void
    {
        $this->resetForm();
        $this->showModal       = false;
        $this->showDeleteModal = false;
    }

    private function resetForm(): void
    {
        $this->planId      = null;
        $this->code        = '';
        $this->name        = '';
        $this->description = '';
        $this->amount      = '';
        $this->currency    = 'MZN';
        $this->features    = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.plans.plan-list', [
            'plans' => Plan::with(['activePrice', 'features'])->latest()->paginate(10),
        ]);
    }
}
