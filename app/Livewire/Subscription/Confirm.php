<?php
namespace App\Livewire\Subscription;

use Livewire\Component;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Auth;

class Confirm extends Component
{
    public Plan $plan;
    public string $phone = '';
    public bool $loading = false;

    protected $rules = [
        'phone' => 'required|regex:/^84|85\d{7}$/'
    ];

    public function pay(MpesaService $mpesa)
    {
        $this->validate();
        $this->loading = true;

        $subscription = Subscription::create([
            'user_id' => Auth::id(),
            'plan_id' => $this->plan->id,
            'price' => $this->plan->currentPrice()->amount,
            'status' => 'pending',
            'start_date' => now(),
            'end_date' => now()->addMonth(),
        ]);

        $response = $mpesa->requestPayment($this->phone, $subscription->price);

        $payment = Payment::create([
            'user_id' => Auth::id(),
            'subscription_id' => $subscription->id,
            'method' => 'mpesa',
            'phone' => $this->phone,
            'amount' => $subscription->price,
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
