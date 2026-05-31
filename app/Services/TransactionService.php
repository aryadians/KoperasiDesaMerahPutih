<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Member;
use App\Services\SavingsService;
use App\Events\ProductStockUpdated;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    protected $savingsService;

    public function __construct(SavingsService $savingsService)
    {
        $this->savingsService = $savingsService;
    }

    /**
     * Process a checkout order.
     *
     * @param int $userId
     * @param array $items Array of ['product_id' => int, 'quantity' => int]
     * @param string $deliveryType 'pickup'|'delivery'
     * @param string $paymentMethod 'cash'|'saldo_sukarela'|'qris_desa'
     * @return Order
     * @throws Exception
     */
    public function checkout(int $userId, array $items, string $deliveryType, string $paymentMethod = 'cash'): Order
    {
        if (empty($items)) {
            throw new Exception("Keranjang belanja kosong.");
        }

        return DB::transaction(function () use ($userId, $items, $deliveryType, $paymentMethod) {
            $totalAmount = 0.00;
            $orderItemsData = [];

            // 1. Process items, lock stock, and compute totals
            foreach ($items as $item) {
                $productId = $item['product_id'];
                $quantity = $item['quantity'];

                if ($quantity <= 0) {
                    throw new Exception("Jumlah kuantitas tidak valid.");
                }

                // Pessimistic locking to prevent race conditions on stock
                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new Exception("Produk dengan ID {$productId} tidak ditemukan.");
                }

                if ($product->current_stock < $quantity) {
                    throw new Exception("Stok untuk produk '{$product->name}' tidak mencukupi (Stok tersedia: {$product->current_stock}).");
                }

                // Deduct stock
                $product->current_stock -= $quantity;
                $product->save();

                event(new ProductStockUpdated($product));

                // Determine price (member vs non-member)
                $user = DB::table('users')->where('id', $userId)->first();
                $isMember = $user && $user->role === 'anggota';

                $price = $isMember ? $product->price_member : $product->price_non_member;
                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_purchase' => $price,
                    'subtotal' => $subtotal,
                ];
            }

            // 2. Calculate loyalty points earned (1 point per Rp 10.000)
            // Points only apply to members
            $pointsEarned = 0;
            $user = DB::table('users')->where('id', $userId)->first();
            if ($user && $user->role === 'anggota') {
                $pointsEarned = (int) floor($totalAmount / 10000);
            }

            // 3. Create the Order
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $order = Order::create([
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'points_earned' => $pointsEarned,
                'payment_status' => 'pending',
                'delivery_type' => $deliveryType,
                'payment_method' => $paymentMethod,
            ]);

            // Debet Saldo Sukarela if payment method is e-wallet
            if ($paymentMethod === 'saldo_sukarela') {
                $member = Member::where('user_id', $userId)->first();
                if (!$member) {
                    throw new Exception("Akun Anda tidak terdaftar sebagai Anggota Koperasi. Saldo Sukarela hanya tersedia untuk anggota.");
                }

                // Deduct Simpanan Sukarela balance
                $this->savingsService->recordDebit(
                    $member->id,
                    'sukarela',
                    $totalAmount,
                    "Pembayaran Order Gerai: {$orderNumber}"
                );

                // Instantly mark as paid
                $order->payment_status = 'paid';
                $order->save();
            }

            // 4. Create Order Items
            foreach ($orderItemsData as $itemData) {
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            // 5. Update Member Points if transaction is already completed (or we can update it immediately/on payment)
            // Let's credit the points on payment or immediately?
            // The instructions say "Implementasikan sistem poin loyalitas berbasis kontribusi transaksi anggota."
            // Let's add them immediately, or when order is marked as paid. We will write a helper to complete payment which credits the points.
            if ($pointsEarned > 0) {
                $member = Member::where('user_id', $userId)->first();
                if ($member) {
                    $member->total_poin += $pointsEarned;
                    $member->save();
                }
            }

            return $order;
        });
    }

    /**
     * Mark an order as paid.
     *
     * @param int $orderId
     * @return Order
     */
    public function markAsPaid(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            if ($order->payment_status !== 'paid') {
                $order->payment_status = 'paid';
                $order->save();
            }
            return $order;
        });
    }

    /**
     * Cancel an order and restore stock.
     *
     * @param int $orderId
     * @return Order
     */
    public function cancelOrder(int $orderId): Order
    {
        return DB::transaction(function () use ($orderId) {
            $order = Order::findOrFail($orderId);
            if ($order->payment_status === 'cancelled') {
                return $order;
            }

            // Restore stocks
            foreach ($order->items as $item) {
                $product = Product::where('id', $item->product_id)->lockForUpdate()->first();
                if ($product) {
                    $product->current_stock += $item->quantity;
                    $product->save();

                    event(new ProductStockUpdated($product));
                }
            }

            // Deduct points if points were credited
            if ($order->points_earned > 0) {
                $member = Member::where('user_id', $order->user_id)->first();
                if ($member) {
                    $member->total_poin = max(0, $member->total_poin - $order->points_earned);
                    $member->save();
                }
            }

            $order->payment_status = 'cancelled';
            $order->save();

            return $order;
        });
    }
}
