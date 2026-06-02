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
    public function checkout(int $userId, array $items, string $deliveryType, string $paymentMethod = 'cash', ?string $voucherCode = null): Order
    {
        if (empty($items)) {
            throw new Exception("Keranjang belanja kosong.");
        }

        return DB::transaction(function () use ($userId, $items, $deliveryType, $paymentMethod, $voucherCode) {
            $totalAmount = 0.00;
            $orderItemsData = [];
            $totalBundleDiscount = 0.00;
            $totalTebusDiscount = 0.00;

            // Determine if the user is a member
            $user = DB::table('users')->where('id', $userId)->first();
            $isMember = $user && $user->role === 'anggota';

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

                $price = $isMember ? $product->price_member : $product->price_non_member;
                
                // Promo Beli 3 Bayar 2 calculation for "Mie" or "Susu" products
                $itemBundleDiscount = 0;
                $nameLower = strtolower($product->name);
                if (str_contains($nameLower, 'mie') || str_contains($nameLower, 'susu')) {
                    $freeQty = floor($quantity / 3);
                    $itemBundleDiscount = $price * $freeQty;
                }
                $totalBundleDiscount += $itemBundleDiscount;

                $subtotal = $price * $quantity;
                $totalAmount += $subtotal;

                $orderItemsData[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'price_at_purchase' => $price,
                    'subtotal' => $subtotal - $itemBundleDiscount,
                    'is_local' => $product->is_local_product,
                ];
            }

            // 2. Promo Tebus Murah: if total gross (after bundle discount) is > Rp 100.000, local products get Rp 5.000 discount per item
            $netBeforeTebus = $totalAmount - $totalBundleDiscount;
            if ($netBeforeTebus > 100000) {
                foreach ($orderItemsData as $key => $itemData) {
                    if (!empty($itemData['is_local'])) {
                        $itemTebusDiscount = 5000 * $itemData['quantity'];
                        $totalTebusDiscount += $itemTebusDiscount;
                        // Deduct from item subtotal
                        $orderItemsData[$key]['subtotal'] = max(0, $orderItemsData[$key]['subtotal'] - $itemTebusDiscount);
                    }
                }
            }

            // Deduct promo bundle and tebus discounts from total amount
            $totalAmount = $totalAmount - $totalBundleDiscount - $totalTebusDiscount;

            // 3. Apply Voucher Coupon
            $voucherDiscount = 0.00;
            if ($voucherCode) {
                $validVouchers = [
                    'HEMATTANI' => ['type' => 'percentage', 'value' => 10, 'min_amount' => 0],
                    'KDKMPMERDEKA' => ['type' => 'flat', 'value' => 15000, 'min_amount' => 50000],
                    'ALFAGIFT3D' => ['type' => 'percentage', 'value' => 20, 'min_amount' => 0],
                ];

                $code = strtoupper($voucherCode);
                if (isset($validVouchers[$code])) {
                    $v = $validVouchers[$code];
                    if ($totalAmount >= $v['min_amount']) {
                        if ($v['type'] === 'percentage') {
                            $voucherDiscount = $totalAmount * ($v['value'] / 100);
                        } else {
                            $voucherDiscount = $v['value'];
                        }
                        $voucherDiscount = min($totalAmount, $voucherDiscount);
                    }
                }
            }
            $totalAmount = max(0, $totalAmount - $voucherDiscount);

            // 4. Calculate loyalty points earned (1 point per Rp 10.000) on final net amount
            $pointsEarned = 0;
            if ($isMember) {
                $pointsEarned = (int) floor($totalAmount / 10000);
            }

            // Get branch ID from the first product's branch
            $firstProductId = count($items) > 0 ? $items[0]['product_id'] : null;
            $firstProduct = $firstProductId ? Product::find($firstProductId) : null;
            $branchId = $firstProduct ? $firstProduct->branch_id : 1;

            // 5. Create the Order
            $orderNumber = 'ORD-' . strtoupper(uniqid());
            $order = Order::create([
                'branch_id' => $branchId,
                'user_id' => $userId,
                'order_number' => $orderNumber,
                'total_amount' => $totalAmount,
                'points_earned' => $pointsEarned,
                'payment_status' => 'pending',
                'delivery_type' => $deliveryType,
                'payment_method' => $paymentMethod,
            ]);

            // Phase 10: Integrate Payment Gateway for non-cash/non-wallet methods
            if ($paymentMethod === 'qris_desa') {
                $paymentService = resolve(\App\Services\PaymentService::class);
                $session = $paymentService->createPaymentSession('qris', (float) $totalAmount, $orderNumber);
                
                $order->payment_gateway_ref = $session['gateway_ref'];
                $order->payment_url = $session['payment_url'];
                $order->save();
            }

            // Debet Saldo Sukarela if payment method is e-wallet / co-op balance
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

            // 6. Create Order Items
            foreach ($orderItemsData as $itemData) {
                unset($itemData['is_local']); // Remove helper key
                $itemData['order_id'] = $order->id;
                OrderItem::create($itemData);
            }

            // 7. Update Member Points if transaction is completed
            if ($pointsEarned > 0) {
                $member = Member::where('user_id', $userId)->first();
                if ($member) {
                    $member->total_poin += $pointsEarned;
                    $member->save();
                }
            }

            // 8. Dispatch Notification
            $notificationService = resolve(\App\Services\NotificationService::class);
            $msg = "Pesanan Anda ({$order->order_number}) senilai Rp " . number_format($totalAmount, 0, ',', '.') . " berhasil dibuat.";
            if ($paymentMethod === 'saldo_sukarela') {
                $msg .= " Pembayaran lunas via Saldo Sukarela.";
            } else {
                $msg .= " Silakan selesaikan pembayaran Anda.";
            }
            $notificationService->createNotification($userId, '🛍️ Pesanan Dibuat', $msg, 'order', $order->id);

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

                // Dispatch Notification
                $notificationService = resolve(\App\Services\NotificationService::class);
                $notificationService->createNotification(
                    $order->user_id,
                    '✅ Pesanan Lunas',
                    "Pembayaran pesanan Anda ({$order->order_number}) sebesar Rp " . number_format((float) $order->total_amount, 0, ',', '.') . " berhasil dikonfirmasi.",
                    'order',
                    $order->id
                );
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

            // Dispatch Notification
            $notificationService = resolve(\App\Services\NotificationService::class);
            $notificationService->createNotification(
                $order->user_id,
                '❌ Pesanan Dibatalkan',
                "Pesanan Anda ({$order->order_number}) telah dibatalkan.",
                'order',
                $order->id
            );

            return $order;
        });
    }
}
