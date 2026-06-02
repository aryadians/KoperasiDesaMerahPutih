<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\LoanPayment;
use App\Services\TransactionService;
use App\Models\ActivityLog;

class PaymentWebhookController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Handle incoming webhook from simulated payment gateway.
     */
    public function handle(Request $request)
    {
        // 1. Basic Signature/Token Validation
        $token = $request->header('X-Gateway-Token');
        $expectedToken = env('FONNTE_TOKEN', 'your-fonnte-api-token'); // Re-using a dummy token for simulation
        
        if ($token !== $expectedToken) {
            ActivityLog::log('webhook_failed', null, null, ['reason' => 'Invalid token', 'ip' => $request->ip()]);
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // 2. Validate payload
        $gatewayRef = $request->input('gateway_ref');
        $status = $request->input('status');

        if (!$gatewayRef || $status !== 'success') {
            return response()->json(['message' => 'Ignored or invalid payload'], 400);
        }

        // 3. Find and process the order or loan payment
        $order = Order::where('payment_gateway_ref', $gatewayRef)->first();
        if ($order) {
            if ($order->payment_status !== 'paid') {
                $this->transactionService->markAsPaid($order->id);
                ActivityLog::log('webhook_success', 'Order', $order->id, ['ref' => $gatewayRef]);
            }
            return response()->json(['message' => 'Order marked as paid']);
        }

        $loanPayment = LoanPayment::where('payment_gateway_ref', $gatewayRef)->first();
        if ($loanPayment) {
            if ($loanPayment->status !== 'paid') {
                $loanPayment->status = 'paid';
                $loanPayment->save();
                
                // If it's a loan payment, we already handled the business logic at creation, 
                // but we should verify the loan status logic here if needed.
                ActivityLog::log('webhook_success', 'LoanPayment', $loanPayment->id, ['ref' => $gatewayRef]);
            }
            return response()->json(['message' => 'Loan payment marked as paid']);
        }

        return response()->json(['error' => 'Reference not found'], 404);
    }
}
