<?php

namespace App\Livewire\Subscription;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\Payment;
use App\Services\MpesaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Say7ama\MpesaSdk\Http\Transactions\MpesaTransactions;


class Confirm extends Component
{
    public Plan $plan;
    public string $phone = '';
    public bool $loading = false;
    public ?string $errorMessage = null;

    // IVA 16%
    const IVA_RATE = 0.16;

    protected function rules(): array
    {
        return [
            'phone' => ['required', 'regex:/^(84|85)[0-9]{7}$/'],
        ];
    }

    protected function messages(): array
    {
        return [
            'phone.required' => 'O número M-Pesa é obrigatório.',
            'phone.regex'    => 'O número deve começar por 84 ou 85 e ter 9 dígitos.',
        ];
    }

    public function getSubtotalProperty(): float
    {
        return $this->plan->currentPrice()->amount;
    }

    public function getIvaProperty(): float
    {
        return round($this->subtotal * self::IVA_RATE, 2);
    }

    public function getTotalProperty(): float
    {
        return round($this->subtotal + $this->iva, 2);
    }

    public function pay(MpesaService $mpesa): void
    {

        $this->errorMessage = null;
        $this->validate();
        $this->loading = true;

        // Verifica se já tem subscrição activa
        $existing = Subscription::where('user_id', Auth::id())
            ->where('plan_id', $this->plan->id)
            ->where('status', 'active')
            ->where('end_date', '>', now())
            ->first();


        if ($existing) {
            $this->errorMessage = 'Já tem uma subscrição activa neste plano.';
            $this->loading = false;
            return;
        }

        DB::beginTransaction();

        try {

            $subscription = Subscription::create([
                'user_id'    => Auth::id(),
                'plan_id'    => $this->plan->id,
                'price'      => $this->plan->currentPrice()->amount,
                'status'     => 'pending',
                'start_date' => now(),
                'end_date'   => now()->addMonth(),
            ]);

            $from = $this->phone;
            $reference = $this->reference(8);
            $transaction = 'T12344C';

            $mpesa = new MpesaTransactions();

            $data = [
                'from' => '258' . $from,
                'reference' => $reference,
                'transaction' => $transaction,
                'amount' => $this->plan->currentPrice()->amount
            ];

            $result = $mpesa->C2BPayment($data);

            Log::info( json_encode($result) );

            if ($result['Statuscode'] == 201) {
               $status ='paid';
            }else{
                $status = 'failed';
            }

            Payment::create([
                'user_id'               => Auth::id(),
                'subscription_id'       => $subscription->id,
                'method'                => 'mpesa',
                'phone'                 => $this->phone,
                'amount'                => $this->plan->currentPrice()->amount,
                'status'                => $status,
                'transaction_reference' => $result['data']['conversationId'] ?? null,
            ]);

            if ($status === 'paid') {
                $subscription->update(['status' => 'active']);
                DB::commit();
                $this->redirect(route('subscription.success'));
            } else {
                $subscription->update(['status' => 'failed']);
                DB::commit();
                $this->errorMessage = 'Pagamento não foi aprovado: ' . $result['message'];
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->errorMessage = 'Ocorreu um erro. Por favor tente novamente.';
            \Log::error('Subscription payment error: ' . $e->getMessage());
        }

        $this->loading = false;
    }
    public static function reference($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return strtoupper(substr(str_shuffle(str_repeat($pool, $length)), 0, $length));
    }
    public function render()
    {
        return view('livewire.subscription.confirm');
    }
}
