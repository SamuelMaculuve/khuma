<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class SubscriptionReceiptController extends Controller
{
    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();
        $payment->load('subscription.plan', 'subscription.company');

        abort_unless(
            $payment->user_id === $user->id
            || ($payment->subscription?->company_id && $payment->subscription->company_id === $user->company_id),
            403
        );

        return response()
            ->view('subscription.receipt', ['payment' => $payment])
            ->header('Content-Type', 'text/html');
    }
}
