<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function download2(Payment $payment)
    {
        // Garante que só o dono pode baixar
        abort_if($payment->user_id !== Auth::id(), 403);

        $payment->load('subscription.plan', 'user');

        $html = view('receipts.payment', compact('payment'))->render();

        // Usa o pacote dompdf (laravel-dompdf) se disponível
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait');

            return $pdf->download("comprovativo-{$payment->id}.pdf");
        }

        // Fallback: devolve HTML imprimível
        return response($html)->header('Content-Type', 'text/html');
    }
    public function download(Payment $payment)
    {
        // Garante que só o dono pode baixar
        abort_if($payment->user_id !== Auth::id(), 403);

        $payment->load('subscription.plan', 'user');

        $html = view('receipts.payment', compact('payment'))->render();

        // Usa o pacote dompdf (laravel-dompdf) se disponível
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html)
                ->setPaper('a4', 'portrait');

            return $pdf->download("comprovativo-{$payment->id}.pdf");
        }

        // Fallback: devolve HTML imprimível
        return response($html)->header('Content-Type', 'text/html');
    }
}
