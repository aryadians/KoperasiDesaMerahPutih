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
        return view('cart.index', compact('cart', 'sukarelaBalance'));
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

        try {
            $order = $this->transactionService->checkout(
                Auth::id(),
                $items,
                $request->delivery_type,
                $request->payment_method
            );

            // Clear cart
            session()->forget('cart');

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pesanan berhasil dibuat! Silakan lanjutkan pembayaran.');
        } catch (Exception $e) {
            return redirect()->route('cart.index')->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }
}
