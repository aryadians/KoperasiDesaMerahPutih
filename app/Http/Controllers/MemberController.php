<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberSaving;
use App\Models\Loan;
use App\Models\CropAbsorption;
use App\Models\Order;
use App\Services\SavingsService;
use App\Services\LoanService;
use App\Services\CropAbsorptionService;
use App\Services\TransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class MemberController extends Controller
{
    protected $savingsService;
    protected $loanService;
    protected $cropService;
    protected $transactionService;

    public function __construct(
        SavingsService $savingsService,
        LoanService $loanService,
        CropAbsorptionService $cropService,
        TransactionService $transactionService
    ) {
        $this->savingsService = $savingsService;
        $this->loanService = $loanService;
        $this->cropService = $cropService;
        $this->transactionService = $transactionService;
    }

    /**
     * Display member dashboard.
     */
    public function dashboard()
    {
        $user = Auth::user();
        if ($user->role !== 'anggota') {
            return redirect()->route('staff.dashboard');
        }

        $member = Member::with(['user'])->where('user_id', $user->id)->firstOrFail();
        
        $savingsBalances = $this->savingsService->getBalances($member->id);
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();
        $activeLoan = Loan::where('member_id', $member->id)->whereIn('status', ['draft', 'approved', 'active'])->first();

        // Check monthly obliged iuran status
        $iuranWajibPaidThisMonth = MemberSaving::where('member_id', $member->id)
            ->where('type', 'wajib')
            ->where('amount', '>', 0)
            ->whereMonth('transaction_date', date('m'))
            ->whereYear('transaction_date', date('Y'))
            ->exists();
        $iuranWajibNominal = (float) (\App\Models\SystemConfig::where('key', 'IURAN_WAJIB_NOMINAL')->first()->value ?? 50000.00);

        return view('member.dashboard', compact('member', 'savingsBalances', 'recentOrders', 'activeLoan', 'iuranWajibPaidThisMonth', 'iuranWajibNominal'));
    }

    /**
     * Show savings ledger.
     */
    public function savings()
    {
        $member = Member::where('user_id', Auth::id())->firstOrFail();
        $savings = MemberSaving::where('member_id', $member->id)->latest()->get();
        $balances = $this->savingsService->getBalances($member->id);

        return view('member.savings', compact('savings', 'balances'));
    }

    /**
     * Handle savings deposit request.
     */
    public function depositSaving(Request $request)
    {
        $request->validate([
            'type' => 'required|in:pokok,wajib,sukarela',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        $member = Member::where('user_id', Auth::id())->firstOrFail();

        try {
            $this->savingsService->recordSaving(
                $member->id,
                $request->type,
                $request->amount,
                $request->notes
            );

            return redirect()->route('member.savings')->with('success', 'Simpanan berhasil disetor.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyetor simpanan: ' . $e->getMessage()]);
        }
    }

    /**
     * Show loan transactions and request form.
     */
    public function loans()
    {
        $member = Member::where('user_id', Auth::id())->firstOrFail();
        $loans = Loan::with('payments')->where('member_id', $member->id)->latest()->get();
        $activeLoan = Loan::with('payments')->where('member_id', $member->id)->whereIn('status', ['draft', 'approved', 'active'])->first();

        return view('member.loans', compact('loans', 'activeLoan'));
    }

    /**
     * Submit micro-loan application.
     */
    public function applyLoan(Request $request)
    {
        $request->validate([
            'amount_requested' => 'required|numeric|min:100000',
            'tenor_months' => 'required|integer|min:1|max:36',
        ]);

        $member = Member::where('user_id', Auth::id())->firstOrFail();
        
        // Standard cooperative rate, e.g., 5.0% flat interest rate
        $interestRate = 5.00;

        try {
            $this->loanService->applyLoan(
                $member->id,
                $request->amount_requested,
                $interestRate,
                $request->tenor_months
            );

            return redirect()->route('member.loans')->with('success', 'Pengajuan pinjaman berhasil didaftarkan. Menunggu verifikasi pengurus.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal mengajukan pinjaman: ' . $e->getMessage()]);
        }
    }

    /**
     * Show crop absorptions history.
     */
    public function crops()
    {
        $member = Member::where('user_id', Auth::id())->firstOrFail();
        $crops = CropAbsorption::where('member_id', $member->id)->latest()->get();
        $localProducts = \App\Models\Product::where('branch_id', $member->user->branch_id)
            ->where('is_local_product', true)
            ->get();

        return view('member.crops', compact('crops', 'localProducts'));
    }

    /**
     * Submit crop selling request.
     */
    public function sellCrop(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0.1',
            'price_per_unit' => 'required|numeric|min:1',
        ]);

        $member = Member::where('user_id', Auth::id())->firstOrFail();

        try {
            $this->cropService->submitAbsorption(
                $member->id,
                $request->product_name,
                $request->quantity,
                $request->price_per_unit
            );

            return redirect()->route('member.crops')->with('success', 'Penawaran hasil tani berhasil diajukan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal menawarkan hasil tani: ' . $e->getMessage()]);
        }
    }

    /**
     * List all orders for member.
     */
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('member.orders', compact('orders'));
    }

    /**
     * Show details of a specific order.
     */
    public function showOrder($id)
    {
        $order = Order::with(['items.product', 'branch'])->where('user_id', Auth::id())->findOrFail($id);
        return view('member.order_details', compact('order'));
    }

    /**
     * Pay order simulating cashier validation or immediate update.
     */
    public function payOrder($id)
    {
        // Authorize order belongs to logged in user
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        try {
            $this->transactionService->markAsPaid($order->id);
            return redirect()->route('orders.show', $order->id)->with('success', 'Pembayaran pesanan berhasil disimulasikan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal memproses pembayaran: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel pending order.
     */
    public function cancelOrder($id)
    {
        // Authorize order belongs to logged in user
        $order = Order::where('user_id', Auth::id())->findOrFail($id);

        try {
            $this->transactionService->cancelOrder($order->id);
            return redirect()->route('orders.show', $order->id)->with('success', 'Pesanan berhasil dibatalkan dan stok dikembalikan.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Gagal membatalkan pesanan: ' . $e->getMessage()]);
        }
    }
}
