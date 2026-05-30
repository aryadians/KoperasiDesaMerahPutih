<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CropAbsorption;
use App\Models\Loan;
use App\Models\Product;
use App\Models\Category;
use App\Models\Member;
use App\Services\TransactionService;
use App\Services\CropAbsorptionService;
use App\Services\LoanService;
use App\Services\SHUService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Exception;

class StaffController extends Controller
{
    protected $transactionService;
    protected $cropService;
    protected $loanService;
    protected $shuService;

    public function __construct(
        TransactionService $transactionService,
        CropAbsorptionService $cropService,
        LoanService $loanService,
        SHUService $shuService
    ) {
        $this->transactionService = $transactionService;
        $this->cropService = $cropService;
        $this->loanService = $loanService;
        $this->shuService = $shuService;
    }

    /**
     * Staff main dashboard.
     */
    public function dashboard()
    {
        $pendingOrdersCount = Order::where('payment_status', 'pending')->count();
        $pendingCropsCount = CropAbsorption::where('status', 'pending')->count();
        $pendingLoansCount = Loan::where('status', 'draft')->count();
        
        $lowStockProducts = Product::where('current_stock', '<', 5)->get();

        // Statistics
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalCropPayout = CropAbsorption::where('status', 'paid')->sum('total_payout');
        $activeLoansVolume = Loan::where('status', 'active')->sum('amount_approved');

        return view('staff.dashboard', compact(
            'pendingOrdersCount',
            'pendingCropsCount',
            'pendingLoansCount',
            'lowStockProducts',
            'totalSales',
            'totalCropPayout',
            'activeLoansVolume'
        ));
    }

    /**
     * Order management list.
     */
    public function orders()
    {
        $orders = Order::with('user')->latest()->get();
        return view('staff.orders', compact('orders'));
    }

    public function updateOrderStatus($id, $status)
    {
        try {
            if ($status === 'paid') {
                $this->transactionService->markAsPaid($id);
            } elseif ($status === 'cancelled') {
                $this->transactionService->cancelOrder($id);
            }
            return back()->with('success', 'Status pesanan berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status: ' . $e->getMessage()]);
        }
    }

    /**
     * Crop absorption management list.
     */
    public function crops()
    {
        $crops = CropAbsorption::with('member.user')->latest()->get();
        return view('staff.crops', compact('crops'));
    }

    public function updateCropStatus($id, $status)
    {
        try {
            $this->cropService->updateStatus($id, $status);
            return back()->with('success', 'Status penyerapan hasil tani berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status penyerapan: ' . $e->getMessage()]);
        }
    }

    /**
     * Loan management list.
     */
    public function loans()
    {
        $loans = Loan::with('member.user')->latest()->get();
        return view('staff.loans', compact('loans'));
    }

    public function updateLoanStatus(Request $request, $id, $status)
    {
        try {
            $amountApproved = $request->input('amount_approved');
            $this->loanService->updateStatus($id, $status, $amountApproved);
            return back()->with('success', 'Status pinjaman berhasil diperbarui.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui status pinjaman: ' . $e->getMessage()]);
        }
    }

    /**
     * Post a loan installment payment.
     */
    public function recordLoanPayment(Request $request)
    {
        $request->validate([
            'loan_id' => 'required|exists:loans,id',
            'amount_paid' => 'required|numeric|min:1000',
            'penalty' => 'required|numeric|min:0',
            'installment_number' => 'required|integer|min:1',
        ]);

        try {
            $this->loanService->recordPayment(
                $request->loan_id,
                $request->amount_paid,
                $request->penalty,
                $request->installment_number
            );

            return back()->with('success', 'Pembayaran angsuran pinjaman berhasil dicatat.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal mencatat pembayaran angsuran: ' . $e->getMessage()]);
        }
    }

    /**
     * Product Inventory CRUD.
     */
    public function products()
    {
        $products = Product::with('category')->get();
        $categories = Category::all();
        return view('staff.products', compact('products', 'categories'));
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_member' => 'required|numeric|min:0',
            'price_non_member' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'is_local_product' => 'nullable|boolean',
        ]);

        Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price_member' => $request->price_member,
            'price_non_member' => $request->price_non_member,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit,
            'is_local_product' => $request->has('is_local_product'),
        ]);

        return back()->with('success', 'Produk berhasil ditambahkan ke inventaris.');
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_member' => 'required|numeric|min:0',
            'price_non_member' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'is_local_product' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price_member' => $request->price_member,
            'price_non_member' => $request->price_non_member,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit,
            'is_local_product' => $request->has('is_local_product'),
        ]);

        return back()->with('success', 'Produk berhasil diperbarui.');
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return back()->with('success', 'Produk berhasil dihapus (Soft Delete).');
    }

    /**
     * SHU calculation screen.
     */
    public function shu(Request $request)
    {
        $distribution = [];
        $totalSHUAmount = $request->input('shu_amount');

        if ($totalSHUAmount && $totalSHUAmount > 0) {
            try {
                $distribution = $this->shuService->calculateSHUDistribution($totalSHUAmount);
            } catch (Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
        }

        return view('staff.shu', compact('distribution', 'totalSHUAmount'));
    }
}
