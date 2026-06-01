<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class CartController extends Controller
{
    protected $transactionService;

    public function __construct(TransactionService $transactionService)
    {
        $this->transactionService = $transactionService;
    }

    /**
     * Show cart view.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $sukarelaBalance = 0;
        if (Auth::check() && Auth::user()->role === 'anggota') {
            $member = Auth::user()->member;
            if ($member) {
                $sukarelaBalance = \App\Models\MemberSaving::where('member_id', $member->id)
                    ->where('type', 'sukarela')
                    ->sum('amount');
            }
        }

        // Calculate dynamic minimarket promos (Beli 3 Bayar 2 & Tebus Murah)
        $totalGross = 0;
        $totalBundleDiscount = 0;
        $totalTebusDiscount = 0;
        
        foreach ($cart as $id => $details) {
            $price = (Auth::check() && Auth::user()->role === 'anggota') 
                ? $details['price_member'] 
                : $details['price_non_member'];
            $baseSubtotal = $price * $details['quantity'];
            $totalGross += $baseSubtotal;

            // 1. Promo Beli 3 Bayar 2 for "Mie" or "Susu" products
            $nameLower = strtolower($details['name']);
            if (str_contains($nameLower, 'mie') || str_contains($nameLower, 'susu')) {
                $freeQty = floor($details['quantity'] / 3);
                $totalBundleDiscount += ($price * $freeQty);
            }
        }

        // 2. Promo Tebus Murah: if total gross (after bundle discount) is > Rp 100.000, local products get Rp 5.000 discount per item
        if (($totalGross - $totalBundleDiscount) > 100000) {
            foreach ($cart as $id => $details) {
                if (!empty($details['is_local_product'])) {
                    $totalTebusDiscount += (5000 * $details['quantity']);
                }
            }
        }

        $totalAfterPromo = $totalGross - $totalBundleDiscount - $totalTebusDiscount;

        // 3. Voucher/Coupon Promo
        $voucherDiscount = 0;
        $activeVoucher = session()->get('active_voucher');
        if ($activeVoucher) {
            if ($totalAfterPromo >= $activeVoucher['min_amount']) {
                if ($activeVoucher['type'] === 'percentage') {
                    $voucherDiscount = $totalAfterPromo * ($activeVoucher['value'] / 100);
                } else {
                    $voucherDiscount = $activeVoucher['value'];
                }
                $voucherDiscount = min($totalAfterPromo, $voucherDiscount);
            } else {
                // Remove voucher if minimum amount is no longer met
                session()->forget('active_voucher');
                $activeVoucher = null;
            }
        }

        $finalTotal = max(0, $totalAfterPromo - $voucherDiscount);

        return view('cart.index', compact(
            'cart', 
            'sukarelaBalance',
            'totalGross',
            'totalBundleDiscount',
            'totalTebusDiscount',
            'activeVoucher',
            'voucherDiscount',
            'finalTotal'
        ));
    }

    /**
     * Add product to cart.
     */
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->product_id;
        $quantity = $request->quantity;

        $product = Product::findOrFail($productId);
        
        // Ensure the product belongs to the active branch (BOLA / Multi-tenant isolation check)
        $currentBranchId = Auth::check() ? Auth::user()->branch_id : session('active_branch_id', 1);
        if ($product->branch_id != $currentBranchId) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak tersedia untuk gerai desa Anda.'
                ], 403);
            }
            return back()->withErrors(['error' => 'Produk tidak tersedia untuk gerai desa Anda.']);
        }

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;
            if ($product->current_stock < $newQuantity) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok '{$product->name}' tidak mencukupi untuk ditambah ke keranjang."
                    ], 422);
                }
                return back()->withErrors(['error' => "Stok '{$product->name}' tidak mencukupi."]);
            }
            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            if ($product->current_stock < $quantity) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => "Stok '{$product->name}' tidak mencukupi."
                    ], 422);
                }
                return back()->withErrors(['error' => "Stok '{$product->name}' tidak mencukupi."]);
            }
            $cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price_member' => $product->price_member,
                'price_non_member' => $product->price_non_member,
                'unit' => $product->unit,
                'quantity' => $quantity,
                'is_local_product' => $product->is_local_product,
            ];
        }

        session()->put('cart', $cart);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'cart_count' => count($cart)
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    /**
     * Remove product from cart.
     */
    public function remove($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->route('cart.index')->with('success', 'Produk dihapus dari keranjang.');
    }

    /**
     * Update cart quantities.
     */
    public function update(Request $request)
    {
        $request->validate([
            'quantities' => 'required|array',
            'quantities.*' => 'required|integer|min:1',
        ]);

        $cart = session()->get('cart', []);

        foreach ($request->quantities as $id => $quantity) {
            if (isset($cart[$id])) {
                $cart[$id]['quantity'] = $quantity;
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Apply Coupon Voucher.
     */
    public function applyVoucher(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = strtoupper($request->code);
        
        $validVouchers = [
            'HEMATTANI' => ['type' => 'percentage', 'value' => 10, 'min_amount' => 0],
            'KDKMPMERDEKA' => ['type' => 'flat', 'value' => 15000, 'min_amount' => 50000],
            'ALFAGIFT3D' => ['type' => 'percentage', 'value' => 20, 'min_amount' => 0],
        ];

        if (!isset($validVouchers[$code])) {
            return back()->with('error', 'Kode voucher tidak valid.');
        }

        session()->put('active_voucher', [
            'code' => $code,
            'type' => $validVouchers[$code]['type'],
            'value' => $validVouchers[$code]['value'],
            'min_amount' => $validVouchers[$code]['min_amount'],
        ]);

        return back()->with('success', "Voucher '{$code}' berhasil diterapkan!");
    }

    /**
     * Remove Coupon Voucher.
     */
    public function removeVoucher()
    {
        session()->forget('active_voucher');
        return back()->with('success', 'Voucher berhasil dihapus.');
    }

    /**
     * Process checkout.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'delivery_type' => 'required|in:pickup,delivery',
            'payment_method' => 'required|in:cash,saldo_sukarela,qris_desa',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Keranjang belanja kosong.');
        }

        // Format items for service
        $items = [];
        foreach ($cart as $productId => $details) {
            $items[] = [
                'product_id' => $productId,
                'quantity' => $details['quantity'],
            ];
        }

        $activeVoucher = session()->get('active_voucher');
        $voucherCode = $activeVoucher ? $activeVoucher['code'] : null;

        try {
            $order = $this->transactionService->checkout(
                Auth::id(),
                $items,
                $request->delivery_type,
                $request->payment_method,
                $voucherCode
            );

            // Clear cart and voucher
            session()->forget('cart');
            session()->forget('active_voucher');

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran.');
        } catch (Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
