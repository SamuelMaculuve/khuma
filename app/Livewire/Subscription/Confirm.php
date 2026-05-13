<?php
namespace App\Livewire\Subscription;

use Livewire\Attributes\Layout;
use Livewire\Component;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class Confirm extends Component
{
    public Plan $plan;
    public string $phone = '';
    public bool $loading = false;

    protected $rules = [
        'phone' => 'required|string|digits:9|starts_with:84,85'
    ];

    public function mount(Plan $plan)
    {
        $this->plan = $plan;
        $this->phone = Auth::user()->phone ?? '';
    }

    public function pay(MpesaService $mpesa)
    {
        $this->validate();
        $this->loading = true;

        $price = $this->plan->currentPrice();

        if (!$price) {
            $this->addError('phone', 'Este plano ainda não tem preço ativo.');
            $this->loading = false;

            return null;
        }

        $user = Auth::user();
        $subscriptionKey = $user->company_id
            ? ['company_id' => $user->company_id]
            : ['user_id' => $user->id];

        $subscription = Subscription::updateOrCreate(
            $subscriptionKey,
            [
                'user_id' => $user->id,
                'company_id' => $user->company_id,
                'plan_id' => $this->plan->id,
                'status' => 'pending',
                'started_at' => now(),
                'renews_at' => now()->addMonth(),
            ]
        );

        $amount = $price->amount * 1.16;
        $response = $mpesa->requestPayment($this->phone, $amount);

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'method' => 'mpesa',
            'phone' => $this->phone,
            'amount' => $amount,
            'status' => $response['success'] ? 'paid' : 'failed',
            'transaction_reference' => $response['transaction_reference'] ?? null
        ]);

        if ($payment->status === 'paid') {
            $subscription->update(['status' => 'active']);
        }

        return redirect()->route('subscription.success');
    }

    public function render()
    {
        return view('livewire.subscription.confirm');
    }
}
