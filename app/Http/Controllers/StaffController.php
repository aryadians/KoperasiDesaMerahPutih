<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CropAbsorption;
use App\Models\Loan;
use App\Models\Product;
use App\Models\Category;
use App\Models\Member;
use App\Models\User;
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
        $branchId = auth()->user()->branch_id;
        $pendingOrdersCount = Order::where('branch_id', $branchId)->where('payment_status', 'pending')->count();
        $pendingCropsCount = CropAbsorption::where('branch_id', $branchId)->where('status', 'pending')->count();
        $pendingLoansCount = Loan::where('branch_id', $branchId)->where('status', 'draft')->count();
        
        $lowStockProducts = Product::where('branch_id', $branchId)->where('current_stock', '<', 5)->get();

        // Statistics
        $totalSales = Order::where('branch_id', $branchId)->where('payment_status', 'paid')->sum('total_amount');
        $totalCropPayout = CropAbsorption::where('branch_id', $branchId)->where('status', 'paid')->sum('total_payout');
        $activeLoansVolume = Loan::where('branch_id', $branchId)->where('status', 'active')->sum('amount_approved');

        // Iuran Wajib and Autodebet stats
        $iuranWajibNominal = (float) (SystemConfig::where('key', 'IURAN_WAJIB_NOMINAL')->first()->value ?? 50000.00);
        $activeMembers = Member::where('status_aktif', true)->whereHas('user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->get();
        
        $paidCount = 0;
        $unpaidCount = 0;
        foreach ($activeMembers as $m) {
            $hasPaid = MemberSaving::where('member_id', $m->id)
                ->where('type', 'wajib')
                ->where('amount', '>', 0)
                ->whereMonth('transaction_date', date('m'))
                ->whereYear('transaction_date', date('Y'))
                ->exists();
            if ($hasPaid) {
                $paidCount++;
            } else {
                $unpaidCount++;
            }
        }

        $autodebetLogs = MemberSaving::with('member.user')
            ->whereHas('member.user', function($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })
            ->where('type', 'sukarela')
            ->where('amount', '<', 0)
            ->where('notes', 'like', '%Autodebet%')
            ->latest()
            ->take(5)
            ->get();

        return view('staff.dashboard', compact(
            'pendingOrdersCount',
            'pendingCropsCount',
            'pendingLoansCount',
            'lowStockProducts',
            'totalSales',
            'totalCropPayout',
            'activeLoansVolume',
            'iuranWajibNominal',
            'paidCount',
            'unpaidCount',
            'autodebetLogs'
        ));
    }

    public function exportOrders(Request $request)
    {
        $request->validate(['type' => 'required|in:pdf,csv', 'ids' => 'nullable|string']);
        
        $branchId = auth()->user()->branch_id;
        $query = Order::with('user')->where('branch_id', $branchId);
        if ($request->filled('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }
        $orders = $query->latest()->get();

        if ($request->type === 'csv') {
            $csvData = "Nomor Pesanan,Warga,Tanggal,Pengiriman,Total,Status Bayar\n";
            foreach($orders as $o) {
                $csvData .= "{$o->order_number},{$o->user->name},{$o->created_at->format('Y-m-d')},{$o->delivery_type},{$o->total_amount},{$o->payment_status}\n";
            }
            return response($csvData)->header('Content-Type', 'text/csv')->header('Content-Disposition', 'attachment; filename="orders_'.date('Ymd').'.csv"');
        } else {
            $data = $orders->map(function($o) {
                return [$o->order_number, $o->user->name, $o->created_at->format('Y-m-d'), $o->delivery_type, number_format($o->total_amount, 0), $o->payment_status];
            });
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('layouts.report', [
                'title' => 'Laporan Pesanan Gerai',
                'headers' => ['No. Pesanan', 'Warga', 'Tanggal', 'Pengiriman', 'Total', 'Status'],
                'data' => $data
            ]);
            return $pdf->download('orders_'.date('Ymd').'.pdf');
        }
    }

    /**
     * Order management list.
     */
    public function orders()
    {
        $branchId = auth()->user()->branch_id;
        $orders = Order::with('user')->where('branch_id', $branchId)->latest()->get();
        return view('staff.orders', compact('orders'));
    }

    public function updateOrderStatus($id, $status)
    {
        $order = Order::findOrFail($id);
        if ($order->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }
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
        $branchId = auth()->user()->branch_id;
        $crops = CropAbsorption::with('member.user')->where('branch_id', $branchId)->latest()->get();
        return view('staff.crops', compact('crops'));
    }

    public function updateCropStatus(Request $request, $id, $status)
    {
        $crop = CropAbsorption::findOrFail($id);
        if ($crop->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }
        try {
            $this->cropService->updateStatus($id, $status, $request->input('scale_image'));
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
        $branchId = auth()->user()->branch_id;
        $loans = Loan::with('member.user')->where('branch_id', $branchId)->latest()->get();
        return view('staff.loans', compact('loans'));
    }

    public function updateLoanStatus(Request $request, $id, $status)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }
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

        $loan = Loan::findOrFail($request->loan_id);
        if ($loan->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }

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
    public function products(Request $request)
    {
        $search = $request->input('search');
        $categoryId = $request->input('category_id');
        $branchId = auth()->user()->branch_id;

        $query = Product::with('category')->where('branch_id', $branchId)->latest();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // Paginate products with query parameters appended
        $products = $query->paginate(10)->withQueryString();
        $categories = Category::all();
        
        return view('staff.products', compact('products', 'categories', 'search', 'categoryId'));
    }

    public function exportProducts()
    {
        $branchId = auth()->user()->branch_id;
        $products = Product::with('category')->where('branch_id', $branchId)->latest()->get();
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
            $branchId = auth()->user()->branch_id;
            Product::whereIn('id', $ids)->where('branch_id', $branchId)->delete();
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
            'image_url' => 'nullable|string',
        ]);

        $product = Product::create([
            'branch_id' => auth()->user()->branch_id,
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
            'image_url' => 'nullable|string',
        ]);

        $product = Product::findOrFail($id);
        if ($product->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }
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

    /**
     * Update product stock level directly via inline AJAX (Stock Opname).
     */
    public function updateProductStockInline(Request $request, $id)
    {
        $request->validate([
            'current_stock' => 'required|integer|min:0',
        ]);

        try {
            $product = Product::findOrFail($id);
            if ($product->branch_id !== auth()->user()->branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aksi cabang tidak sah.'
                ], 403);
            }
            $product->current_stock = $request->current_stock;
            $product->save();

            // Dispatch real-time event
            event(new \App\Events\ProductStockUpdated($product));

            return response()->json([
                'success' => true,
                'message' => 'Stok berhasil diperbarui!',
                'current_stock' => $product->current_stock,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui stok: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id);
        if ($product->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }
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
        $branchId = auth()->user()->branch_id;
        $products = Product::with('category')->where('branch_id', $branchId)->where('current_stock', '>', 0)->get();
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
            $amount = (float) (SystemConfig::where('key', 'IURAN_WAJIB_NOMINAL')->first()->value ?? 50000.00);
            $successMembers = [];

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
                    $successMembers[] = $member;
                } else {
                    $failCount++;
                }
            }
            DB::commit();

            // Dispatch WhatsApp notifications
            $notificationService = resolve(\App\Services\NotificationService::class);
            foreach ($successMembers as $sMember) {
                /** @var \App\Models\Member $sMember */
                $sMember->load('user');
                $title = '💸 Autodebet Iuran Bulanan';
                $message = "Autodebet iuran wajib bulanan sebesar Rp " . number_format($amount, 0, ',', '.') . " dari saldo Simpanan Sukarela Anda telah berhasil diproses. Terima kasih.";
                $notificationService->sendMemberNotification($sMember, $title, $message);
            }

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
        $branchId = auth()->user()->branch_id;

        $salesToday = Order::where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('total_amount');
        
        $totalSales = Order::where('branch_id', $branchId)->where('payment_status', 'paid')->sum('total_amount');
        $totalCrops = CropAbsorption::where('branch_id', $branchId)->where('status', 'paid')->sum('total_payout');
        $totalLoans = Loan::where('branch_id', $branchId)->whereIn('status', ['active', 'paid_off'])->sum('amount_approved');
        $totalSavings = MemberSaving::whereHas('member.user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->sum('amount');

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
            'IURAN_WAJIB_NOMINAL' => SystemConfig::where('key', 'IURAN_WAJIB_NOMINAL')->first()->value ?? '50000',
            'IURAN_POKOK_NOMINAL' => SystemConfig::where('key', 'IURAN_POKOK_NOMINAL')->first()->value ?? '100000',
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
            'iuran_wajib_nominal' => 'required|numeric|min:0',
            'iuran_pokok_nominal' => 'required|numeric|min:0',
        ]);

        try {
            SystemConfig::updateOrCreate(['key' => 'APP_NAME'], ['value' => $request->app_name]);
            SystemConfig::updateOrCreate(['key' => 'APP_ENV'], ['value' => $request->app_env]);
            SystemConfig::updateOrCreate(['key' => 'APP_DEBUG'], ['value' => $request->app_debug]);
            SystemConfig::updateOrCreate(['key' => 'SESSION_DRIVER'], ['value' => $request->session_driver]);
            SystemConfig::updateOrCreate(['key' => 'SESSION_LIFETIME'], ['value' => $request->session_lifetime]);
            SystemConfig::updateOrCreate(['key' => 'IURAN_WAJIB_NOMINAL'], ['value' => $request->iuran_wajib_nominal]);
            SystemConfig::updateOrCreate(['key' => 'IURAN_POKOK_NOMINAL'], ['value' => $request->iuran_pokok_nominal]);

            // Clear config cache programmatically
            Artisan::call('optimize:clear');

            return back()->with('success', 'Konfigurasi sistem berhasil disimpan dan cache dibersihkan!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan konfigurasi: ' . $e->getMessage()]);
        }
    }

    /**
     * Export a specific POS transaction receipt as a PDF.
     */
    public function downloadReceiptPdf($id)
    {
        $order = Order::with(['items.product', 'user', 'branch'])->findOrFail($id);
        if ($order->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }

        try {
            $member = null;
            if ($order->user && $order->user->role === 'anggota') {
                $member = Member::where('user_id', $order->user_id)->first();
            }

            // Generate thermal layout PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('staff.receipt_pdf', compact('order', 'member'));
            
            // 80mm thermal roll is approximately 226 points wide
            // Height is set to 550 points to fit common retail orders
            $pdf->setPaper([0, 0, 226, 550], 'portrait');
            
            return $pdf->download("struk_{$order->order_number}.pdf");
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengunduh struk PDF: ' . $e->getMessage()]);
        }
    }

    /**
     * Show registered members and staff list.
     */
    public function members(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $query = User::with(['member', 'branch'])->where('branch_id', $branchId);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhereHas('member', function($mq) use ($search) {
                      $mq->where('nik', 'like', '%' . $search . '%')
                         ->orWhere('nomor_anggota', 'like', '%' . $search . '%');
                  });
            });
        }

        // Role filter
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $users = $query->latest()->paginate(10);
        $roles = ['anggota' => 'Anggota', 'kasir' => 'Kasir', 'pengurus' => 'Pengurus'];

        return view('staff.members', compact('users', 'roles'));
    }

    /**
     * Store new member / staff account.
     */
    public function storeMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:anggota,kasir,pengurus',
            'status' => 'required|in:active,inactive',
            'nik' => 'required_if:role,anggota|nullable|string|size:16|unique:members,nik',
            'alamat_desa' => 'required_if:role,anggota|nullable|string',
            'ktp_image' => 'nullable|string',
            'no_hp' => 'required_if:role,anggota|nullable|string|max:20',
        ], [
            'email.unique' => 'Alamat email sudah terdaftar.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.required_if' => 'NIK wajib diisi untuk peran Anggota.',
            'alamat_desa.required_if' => 'Alamat desa wajib diisi untuk peran Anggota.',
        ]);

        try {
            DB::transaction(function() use ($request) {
                $branchId = auth()->user()->branch_id;

                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => \Illuminate\Support\Facades\Hash::make($request->password),
                    'role' => $request->role,
                    'status' => $request->status,
                    'branch_id' => $branchId,
                ]);

                if ($request->role === 'anggota') {
                    $nomorAnggota = 'MBR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                    Member::create([
                        'user_id' => $user->id,
                        'nik' => $request->nik,
                        'nomor_anggota' => $nomorAnggota,
                        'alamat_desa' => $request->alamat_desa,
                        'tanggal_bergabung' => date('Y-m-d'),
                        'total_poin' => 0,
                        'status_aktif' => $request->status === 'active',
                        'ktp_image' => $request->ktp_image,
                        'no_hp' => $request->no_hp,
                    ]);
                }
            });

            return redirect()->route('staff.members')->with('success', 'Akun pengguna berhasil ditambahkan!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menambahkan akun: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update member / staff account.
     */
    public function updateMember(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if ($user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:anggota,kasir,pengurus',
            'status' => 'required|in:active,inactive',
            'nik' => 'required_if:role,anggota|nullable|string|size:16|unique:members,nik,' . ($user->member->id ?? 0),
            'alamat_desa' => 'required_if:role,anggota|nullable|string',
            'ktp_image' => 'nullable|string',
            'no_hp' => 'required_if:role,anggota|nullable|string|max:20',
        ], [
            'email.unique' => 'Alamat email sudah terdaftar.',
            'nik.unique' => 'NIK sudah terdaftar.',
            'nik.required_if' => 'NIK wajib diisi untuk peran Anggota.',
            'alamat_desa.required_if' => 'Alamat desa wajib diisi untuk peran Anggota.',
        ]);

        try {
            DB::transaction(function() use ($request, $user) {
                $user->name = $request->name;
                $user->email = $request->email;
                if ($request->filled('password')) {
                    $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
                }
                $user->role = $request->role;
                $user->status = $request->status;
                $user->save();

                if ($request->role === 'anggota') {
                    $memberData = [
                        'nik' => $request->nik,
                        'alamat_desa' => $request->alamat_desa,
                        'status_aktif' => $request->status === 'active',
                        'no_hp' => $request->no_hp,
                    ];
                    if ($request->filled('ktp_image')) {
                        $memberData['ktp_image'] = $request->ktp_image;
                    }
                    if (!$user->member) {
                        $memberData['nomor_anggota'] = 'MBR-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
                        $memberData['tanggal_bergabung'] = date('Y-m-d');
                        $memberData['total_poin'] = 0;
                    }
                    Member::updateOrCreate(['user_id' => $user->id], $memberData);
                } else {
                    // If changed to other role, delete member profile if exists
                    if ($user->member) {
                        $user->member->delete();
                    }
                }
            });

            return redirect()->route('staff.members')->with('success', 'Akun pengguna berhasil diperbarui!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memperbarui akun: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete user account.
     */
    public function deleteMember($id)
    {
        $user = User::findOrFail($id);
        if ($user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Unauthorized branch action');
        }

        try {
            if ($user->id === auth()->id()) {
                return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif digunakan.']);
            }

            DB::transaction(function() use ($user) {
                if ($user->member) {
                    $user->member->delete();
                }
                $user->delete();
            });

            return redirect()->route('staff.members')->with('success', 'Akun pengguna berhasil dihapus!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menghapus akun: ' . $e->getMessage()]);
        }
    }

    /**
     * Bulk delete user accounts.
     */
    public function bulkDeleteMembers(Request $request)
    {
        $request->validate(['ids' => 'required|string']);

        try {
            $ids = explode(',', $request->ids);
            $branchId = auth()->user()->branch_id;
            
            DB::transaction(function() use ($ids, $branchId) {
                foreach ($ids as $id) {
                    if (intval($id) === auth()->id()) {
                        continue; // Skip self deletion
                    }
                    $user = User::where('branch_id', $branchId)->find($id);
                    if ($user) {
                        if ($user->member) {
                            $user->member->delete();
                        }
                        $user->delete();
                    }
                }
            });

            return redirect()->route('staff.members')->with('success', 'Akun-akun pengguna terpilih berhasil dihapus!');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal melakukan penghapusan massal: ' . $e->getMessage()]);
        }
    }

    /**
     * Export members to CSV.
     */
    public function exportMembers(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $query = User::with('member')->where('branch_id', $branchId);

        if ($request->filled('ids')) {
            $query->whereIn('id', explode(',', $request->ids));
        }

        $users = $query->latest()->get();

        $csvData = "\xEF\xBB\xBF"; // UTF-8 BOM for Excel Indonesian local characters
        $csvData .= "Nama,Email,Peran,Status,NIK,Nomor Anggota,Alamat Desa,Tanggal Bergabung,Poin Loyalitas\n";
        
        foreach ($users as $u) {
            $nik = $u->member ? ($u->member->nik ?? '-') : '-';
            $nomorAnggota = $u->member ? ($u->member->nomor_anggota ?? '-') : '-';
            $alamat = ($u->member && $u->member->alamat_desa) ? '"' . str_replace('"', '""', $u->member->alamat_desa) . '"' : '-';
            $tgl = ($u->member && $u->member->tanggal_bergabung) ? $u->member->tanggal_bergabung->format('Y-m-d') : '-';
            $poin = $u->member ? ($u->member->total_poin ?? '0') : '0';
            
            $csvData .= "\"{$u->name}\",\"{$u->email}\",\"{$u->role}\",\"{$u->status}\",\"{$nik}\",\"{$nomorAnggota}\",{$alamat},\"{$tgl}\",\"{$poin}\"\n";
        }

        return response($csvData)
            ->header('Content-Type', 'text/csv; charset=UTF-8')
            ->header('Content-Disposition', 'attachment; filename="data_anggota_staf_' . date('Ymd') . '.csv"');
    }

    /**
     * Export Rapat Anggota Tahunan (RAT) branch pertanggungjawaban report as PDF.
     */
    public function exportRATReportPdf()
    {
        $branchId = auth()->user()->branch_id;
        $branch = \App\Models\Branch::findOrFail($branchId);
        $staffName = auth()->user()->name;

        // Financial volumes
        $totalSales = Order::where('branch_id', $branchId)->where('payment_status', 'paid')->sum('total_amount');
        $totalCrops = CropAbsorption::where('branch_id', $branchId)->where('status', 'paid')->sum('total_payout');
        $totalLoans = Loan::where('branch_id', $branchId)->where('status', 'active')->sum('amount_approved');
        $totalSavings = MemberSaving::whereHas('member.user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->sum('amount');

        // Operations metrics
        $activeMembersCount = Member::where('status_aktif', true)->whereHas('user', function($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->count();
        $activeProductsCount = Product::where('branch_id', $branchId)->count();
        
        $salesToday = Order::where('branch_id', $branchId)
            ->where('payment_status', 'paid')
            ->whereDate('created_at', \Carbon\Carbon::today())
            ->sum('total_amount');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('staff.rat_report_pdf', compact(
            'branch',
            'staffName',
            'totalSales',
            'totalCrops',
            'totalLoans',
            'totalSavings',
            'activeMembersCount',
            'activeProductsCount',
            'salesToday'
        ));

        return $pdf->download('laporan_rat_' . strtolower(str_replace(' ', '_', $branch->name)) . '.pdf');
    }
}
