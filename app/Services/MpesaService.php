<?php

namespace App\Services;

class MpesaService
{
    public function requestPayment(string $phone, float $amount): array
    {
        // Aqui entra a integração real com API M-Pesa
        // Simulação para já

        return [
            'success' => true,
            'transaction_reference' => 'MPESA-' . now()->timestamp,
        ];
    }
}
