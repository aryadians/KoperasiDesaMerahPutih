<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class PurchaseOrderController extends Controller
{
    /**
     * Show PO lists and creation dashboard.
     */
    public function index()
    {
        $branchId = auth()->user()->branch_id;
        $purchaseOrders = PurchaseOrder::whereHas('product', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->with('product')->latest()->get();
        $products = Product::where('branch_id', $branchId)->get();
        $categories = Category::all();

        return view('staff.purchase_orders', compact('purchaseOrders', 'products', 'categories'));
    }

    /**
     * Store a new Purchase Order in draft.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'cost_price' => 'required|numeric|min:0',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            if ($product->branch_id !== auth()->user()->branch_id) {
                abort(403, 'Unauthorized branch action');
            }
            $poNumber = 'PO-' . strtoupper(uniqid());

            PurchaseOrder::create([
                'po_number' => $poNumber,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'cost_price' => $request->cost_price,
                'selling_price_member' => $product->price_member,
                'selling_price_non_member' => $product->price_non_member,
                'total_cost' => $request->quantity * $request->cost_price,
                'status' => 'draft',
            ]);

            return back()->with('success', 'Purchase Order berhasil dibuat dalam status draft.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal membuat Purchase Order: ' . $e->getMessage()]);
        }
    }

    /**
     * Update status of a Purchase Order (ordered, received, cancelled).
     */
    public function updateStatus($id, $status)
    {
        if (!in_array($status, ['ordered', 'received', 'cancelled'])) {
            return back()->withErrors(['error' => 'Status PO tidak valid.']);
        }

        try {
            DB::beginTransaction();

            $po = PurchaseOrder::with('product')->findOrFail($id);
            if ($po->product->branch_id !== auth()->user()->branch_id) {
                abort(403, 'Unauthorized branch action');
            }

            if ($po->status === 'received') {
                throw new Exception("Purchase Order ini sudah diterima sebelumnya.");
            }

            if ($status === 'received') {
                // Increment product stock
                $product = Product::findOrFail($po->product_id);
                $product->current_stock += $po->quantity;
                $product->save();

                // Flash WhatsApp Notification Simulation
                session()->flash('sms_notification', [
                    'title' => '📦 Stok Ritel Bertambah',
                    'message' => "Supplier mengirimkan barang. Purchase Order {$po->po_number} telah Diterima. Stok {$product->name} kini bertambah {$po->quantity} {$product->unit}."
                ]);
            }

            $po->status = $status;
            $po->save();

            DB::commit();
            return back()->with('success', "Status Purchase Order berhasil diubah menjadi {$status}.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal merubah status PO: ' . $e->getMessage()]);
        }
    }
}
