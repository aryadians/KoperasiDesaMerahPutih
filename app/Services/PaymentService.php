<?php

namespace App\Services;

use Illuminate\Support\Str;

class PaymentService
{
    /**
     * Generate a simulated payment session for QRIS or VA.
     * In a real implementation, this would call Midtrans/Xendit API.
     */
    public function createPaymentSession(string $type, float $amount, string $reference)
    {
        $gatewayRef = 'PAY-' . strtoupper(Str::random(10));
        
        // Mock payment URL
        $paymentUrl = "https://checkout.simulated-gateway.com/{$gatewayRef}?type={$type}&amount={$amount}";

        return [
            'gateway_ref' => $gatewayRef,
            'payment_url' => $paymentUrl,
        ];
    }

    /**
     * Simulate a webhook callback to confirm payment.
     */
    public function simulateWebhook(string $gatewayRef)
    {
        // This would be called by the gateway to notify us of a successful payment.
        return [
            'status' => 'success',
            'gateway_ref' => $gatewayRef,
            'paid_at' => now(),
        ];
    }
}
