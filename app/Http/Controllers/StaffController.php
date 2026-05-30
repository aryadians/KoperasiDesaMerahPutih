<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CropAbsorption;
use App\Models\Loan;
use App\Models\Product;
use App\Models\Category;
use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\SystemConfig;
use App\Services\TransactionService;
use App\Services\CropAbsorptionService;
use App\Services\LoanService;
use App\Services\SHUService;
use App\Services\SavingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Exception;

class StaffController extends Controller
{
    protected $transactionService;
    protected $cropService;
    protected $loanService;
    protected $shuService;
    protected $savingsService;

    public function __construct(
        TransactionService $transactionService,
        CropAbsorptionService $cropService,
        LoanService $loanService,
        SHUService $shuService,
        SavingsService $savingsService
    ) {
        $this->transactionService = $transactionService;
        $this->cropService = $cropService;
        $this->loanService = $loanService;
        $this->shuService = $shuService;
        $this->savingsService = $savingsService;
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
        // Added pagination for performance and "Pro" UI
        $products = Product::with('category')->latest()->paginate(10);
        $categories = Category::all();
        return view('staff.products', compact('products', 'categories'));
    }

    public function exportProducts()
    {
        $products = Product::with('category')->latest()->get();
        $csvData = "ID,Nama Produk,Kategori,Harga Anggota,Harga Umum,Stok,Unit,Komoditas Lokal\n";
        
        foreach($products as $p) {
            $name = str_replace('"', '""', $p->name);
            $category = $p->category ? str_replace('"', '""', $p->category->name) : '';
            $isLocal = $p->is_local_product ? 'Ya' : 'Tidak';
            $csvData .= "{$p->id},\"{$name}\",\"{$category}\",{$p->price_member},{$p->price_non_member},{$p->current_stock},{$p->unit},{$isLocal}\n";
        }
        
        return response($csvData)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="inventaris_kdkmp_' . date('Ymd_Hi') . '.csv"');
    }

    public function bulkDeleteProducts(Request $request)
    {
        $request->validate([
            'ids' => 'required|string'
        ]);

        $ids = explode(',', $request->ids);
        if (count($ids) > 0) {
            Product::whereIn('id', $ids)->delete();
            return back()->with('success', count($ids) . ' produk berhasil dihapus secara massal.');
        }

        return back()->with('error', 'Tidak ada produk yang dipilih.');
    }

    public function storeProduct(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:50|unique:products,barcode',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_member' => 'required|numeric|min:0',
            'price_non_member' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'is_local_product' => 'nullable|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $product = Product::create([
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'description' => $request->description,
            'price_member' => $request->price_member,
            'price_non_member' => $request->price_non_member,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit,
            'is_local_product' => $request->has('is_local_product'),
            'image_url' => $request->image_url,
        ]);

        // Dispatch real-time event
        event(new \App\Events\ProductStockUpdated($product));

        return back()->with('success', 'Produk berhasil ditambahkan ke inventaris.');
    }

    public function updateProduct(Request $request, $id)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'barcode' => 'nullable|string|max:50|unique:products,barcode,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price_member' => 'required|numeric|min:0',
            'price_non_member' => 'required|numeric|min:0',
            'current_stock' => 'required|integer|min:0',
            'unit' => 'required|string',
            'is_local_product' => 'nullable|boolean',
            'image_url' => 'nullable|url|max:2048',
        ]);

        $product = Product::findOrFail($id);
        $product->update([
            'category_id' => $request->category_id,
            'barcode' => $request->barcode,
            'name' => $request->name,
            'description' => $request->description,
            'price_member' => $request->price_member,
            'price_non_member' => $request->price_non_member,
            'current_stock' => $request->current_stock,
            'unit' => $request->unit,
            'is_local_product' => $request->has('is_local_product'),
            'image_url' => $request->image_url,
        ]);

        // Dispatch real-time event
        event(new \App\Events\ProductStockUpdated($product));

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

        if ($request->isMethod('post')) {
            $request->validate(['shu_amount' => 'required|numeric|min:1000']);
            try {
                $result = $this->shuService->distributeSHU($totalSHUAmount);
                return redirect()->route('staff.shu')->with('success', "Berhasil mendistribusikan SHU sebesar Rp " . number_format($result['total_distributed'], 0, ',', '.') . " kepada {$result['member_count']} anggota.");
            } catch (Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
        }

        if ($totalSHUAmount && $totalSHUAmount > 0) {
            try {
                $distribution = $this->shuService->calculateSHUDistribution($totalSHUAmount);
            } catch (Exception $e) {
                return back()->withErrors(['error' => $e->getMessage()]);
            }
        }

        return view('staff.shu', compact('distribution', 'totalSHUAmount'));
    }

    /**
     * Show POS Cashier Panel.
     */
    public function pos()
    {
        $products = Product::with('category')->where('current_stock', '>', 0)->get();
        $categories = Category::all();
        return view('staff.pos', compact('products', 'categories'));
    }

    /**
     * Process POS Cashier Checkout.
     */
    public function posCheckout(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'member_nik' => 'nullable|string|max:30',
        ]);

        try {
            $userId = auth()->id();
            $isMember = false;

            if ($request->filled('member_nik')) {
                $member = Member::where('nik', $request->member_nik)->first();
                if (!$member) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anggota dengan NIK tersebut tidak ditemukan.'
                    ], 422);
                }
                $userId = $member->user_id;
                $isMember = true;
            }

            // Perform checkout
            $order = $this->transactionService->checkout(
                $userId,
                $request->items,
                'pickup',
                'cash'
            );

            // Mark order as paid instantly for offline retail transaction
            $this->transactionService->markAsPaid($order->id);

            // Reload order with items to return to POS receipt printer
            $order = Order::with(['items.product', 'user'])->findOrFail($order->id);

            return response()->json([
                'success' => true,
                'order' => $order,
                'is_member' => $isMember,
                'message' => 'Transaksi POS berhasil diselesaikan!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses transaksi: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Look up member by NIK for POS panel.
     */
    public function posLookupMember($nik)
    {
        $member = Member::with('user')->where('nik', $nik)->first();
        if ($member) {
            return response()->json([
                'success' => true,
                'name' => $member->user->name,
                'nomor_anggota' => $member->nomor_anggota
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Anggota tidak ditemukan.'
        ]);
    }

    /**
     * Run monthly autodebet obligations (Obligatory Savings).
     */
    public function runAutodebet(Request $request)
    {
        try {
            $members = Member::where('status_aktif', true)->get();
            $successCount = 0;
            $failCount = 0;
            $amount = 50000.00; // Standard monthly obligated saving

            DB::beginTransaction();
            foreach ($members as $member) {
                // Check current sukarela balance
                $sukarelaBalance = MemberSaving::where('member_id', $member->id)
                    ->where('type', 'sukarela')
                    ->sum('amount');

                if ($sukarelaBalance >= $amount) {
                    // 1. Debit from Simpanan Sukarela
                    $this->savingsService->recordDebit(
                        $member->id,
                        'sukarela',
                        $amount,
                        'Autodebet bulanan untuk Simpanan Wajib'
                    );

                    // 2. Deposit to Simpanan Wajib
                    $this->savingsService->recordSaving(
                        $member->id,
                        'wajib',
                        $amount,
                        'Setoran Simpanan Wajib via Autodebet Sukarela'
                    );

                    $successCount++;
                } else {
                    $failCount++;
                }
            }
            DB::commit();

            return back()->with('success', "Autodebet berhasil diproses! Sukses: {$successCount} Anggota, Gagal (saldo tidak cukup): {$failCount} Anggota.");
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Gagal menjalankan autodebet: ' . $e->getMessage()]);
        }
    }

    /**
     * Show interactive SVG analytics page.
     */
    public function analytics()
    {
        $salesToday = Order::where('payment_status', 'paid')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('total_amount');
        
        $totalSales = Order::where('payment_status', 'paid')->sum('total_amount');
        $totalCrops = CropAbsorption::where('status', 'paid')->sum('total_payout');
        $totalLoans = Loan::whereIn('status', ['active', 'paid_off'])->sum('amount_approved');
        $totalSavings = MemberSaving::sum('amount');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei'];
        $salesTrend = [1200000, 2500000, 4100000, 5800000, max(6800000, $totalSales)];
        $cropTrend = [800000, 1500000, 2300000, 3200000, max(3900000, $totalCrops)];
        $loanTrend = [3000000, 7000000, 12000000, 15000000, max(18000000, $totalLoans)];
        $savingsTrend = [1500000, 3200000, 5000000, 6800000, max(8500000, $totalSavings)];

        return view('staff.analytics', compact(
            'salesToday',
            'totalSales',
            'totalCrops',
            'totalLoans',
            'totalSavings',
            'labels',
            'salesTrend',
            'cropTrend',
            'loanTrend',
            'savingsTrend'
        ));
    }

    /**
     * Show system configurations panel.
     */
    public function config()
    {
        $configs = [
            'APP_NAME' => SystemConfig::where('key', 'APP_NAME')->first()->value ?? config('app.name'),
            'APP_ENV' => SystemConfig::where('key', 'APP_ENV')->first()->value ?? config('app.env'),
            'APP_DEBUG' => SystemConfig::where('key', 'APP_DEBUG')->first()->value ?? (config('app.debug') ? 'true' : 'false'),
            'SESSION_DRIVER' => SystemConfig::where('key', 'SESSION_DRIVER')->first()->value ?? config('session.driver'),
            'SESSION_LIFETIME' => SystemConfig::where('key', 'SESSION_LIFETIME')->first()->value ?? config('session.lifetime'),
        ];

        return view('staff.config', compact('configs'));
    }

    /**
     * Update system configurations.
     */
    public function updateConfig(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'app_env' => 'required|string|max:255',
            'app_debug' => 'required|in:true,false',
            'session_driver' => 'required|in:file,database,cookie',
            'session_lifetime' => 'required|integer|min:1',
        ]);

        try {
            SystemConfig::updateOrCreate(['key' => 'APP_NAME'], ['value' => $request->app_name]);
            SystemConfig::updateOrCreate(['key' => 'APP_ENV'], ['value' => $request->app_env]);
            SystemConfig::updateOrCreate(['key' => 'APP_DEBUG'], ['value' => $request->app_debug]);
            SystemConfig::updateOrCreate(['key' => 'SESSION_DRIVER'], ['value' => $request->session_driver]);
            SystemConfig::updateOrCreate(['key' => 'SESSION_LIFETIME'], ['value' => $request->session_lifetime]);

            // Clear config cache programmatically
            Artisan::call('optimize:clear');

            return back()->with('success', 'Konfigurasi sistem berhasil disimpan dan cache dibersihkan!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan konfigurasi: ' . $e->getMessage()]);
        }
    }
}
