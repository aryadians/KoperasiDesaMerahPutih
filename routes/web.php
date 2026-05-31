<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\StaffController;

// =====================================================================
// ROOT: Render welcome landing page (guest storefront link inside welcome)
// =====================================================================
Route::get('/', function () {
    return view('welcome');
})->name('home');

// =====================================================================
// ADMIN / STAFF LOGIN — Panel khusus admin, pengurus, kasir, staf
// URL: /admin/login
// =====================================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
});

// =====================================================================
// MEMBER / ANGGOTA LOGIN & REGISTER — Untuk warga desa yang mau
// mendaftar sebagai anggota koperasi dan checkout belanja.
// URL: /login, /register, /forgot-password, /reset-password
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Password Reset
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// =====================================================================
// PUBLIC STOREFRONT — Semua warga bisa browse tanpa login
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/agro-dashboard', [CatalogController::class, 'agroDashboard'])->name('catalog.agro');
Route::get('/catalog/product/{id}', [CatalogController::class, 'show'])->name('catalog.show');
Route::post('/catalog/set-branch/{id}', [CatalogController::class, 'setBranch'])->name('catalog.set-branch');

// Keranjang belanja — guest bisa tambah cart (tapi checkout butuh login anggota)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// =====================================================================
// AUTHENTICATED ROUTES — Anggota & Staff (requires login)
// =====================================================================
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Central dashboard routing by role
    Route::get('/dashboard', function () {
        $role = auth()->user()->role;
        if ($role === 'anggota') {
            return redirect()->route('member.dashboard');
        }
        return redirect()->route('staff.dashboard');
    })->name('dashboard');

    // Checkout requires member login
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // ── MEMBER ROUTES ──────────────────────────────────────────────
    Route::prefix('member')->name('member.')->group(function () {
        Route::get('/dashboard', [MemberController::class, 'dashboard'])->name('dashboard');
        Route::get('/savings', [MemberController::class, 'savings'])->name('savings');
        Route::get('/savings/pdf', [MemberController::class, 'exportSavingsPdf'])->name('savings.pdf');
        Route::post('/savings/deposit', [MemberController::class, 'depositSaving'])->name('savings.deposit');
        Route::get('/loans', [MemberController::class, 'loans'])->name('loans');
        Route::get('/loans/{id}/pdf', [MemberController::class, 'exportLoanPdf'])->name('loans.pdf');
        Route::post('/loans/apply', [MemberController::class, 'applyLoan'])->name('loans.apply');
        Route::get('/crops', [MemberController::class, 'crops'])->name('crops');
        Route::post('/crops/sell', [MemberController::class, 'sellCrop'])->name('crops.sell');
        Route::get('/orders', [MemberController::class, 'orders'])->name('orders');
    });

    // Order detail & actions
    Route::get('/orders/{id}', [MemberController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/pay', [MemberController::class, 'payOrder'])->name('orders.pay');
    Route::post('/orders/{id}/cancel', [MemberController::class, 'cancelOrder'])->name('orders.cancel');

    // ── STAFF / MANAGEMENT ROUTES ──────────────────────────────────
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        Route::get('/analytics', [StaffController::class, 'analytics'])->name('analytics');
        Route::get('/analytics/rat/pdf', [StaffController::class, 'exportRATReportPdf'])->name('analytics.rat-pdf');
        
        // POS Kasir Offline
        Route::get('/pos', [StaffController::class, 'pos'])->name('pos');
        Route::post('/pos/checkout', [StaffController::class, 'posCheckout'])->name('pos.checkout');
        Route::get('/pos/member/{nik}', [StaffController::class, 'posLookupMember'])->name('pos.member');
        Route::get('/pos/receipt/{id}/pdf', [StaffController::class, 'downloadReceiptPdf'])->name('pos.receipt-pdf');
        Route::post('/autodebet', [StaffController::class, 'runAutodebet'])->name('autodebet');

        // System Config Panel
        Route::get('/config', [StaffController::class, 'config'])->name('config');
        Route::post('/config', [StaffController::class, 'updateConfig'])->name('config.update');

        // Procurement / Purchase Orders
        Route::get('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('purchase-orders');
        Route::post('/purchase-orders', [\App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::post('/purchase-orders/{id}/status/{status}', [\App\Http\Controllers\PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');

        // Order management
        Route::get('/orders/export', [StaffController::class, 'exportOrders'])->name('orders.export');
        Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
        Route::post('/orders/{id}/{status}', [StaffController::class, 'updateOrderStatus'])->name('orders.update');

        // Crop absorption (Pengurus Agro)
        Route::get('/crops', [StaffController::class, 'crops'])->name('crops');
        Route::post('/crops/{id}/{status}', [StaffController::class, 'updateCropStatus'])->name('crops.update');

        // Loan underwriting & repayment (Pengurus Finansial)
        Route::get('/loans', [StaffController::class, 'loans'])->name('loans');
        Route::post('/loans/{id}/{status}', [StaffController::class, 'updateLoanStatus'])->name('loans.update');
        Route::post('/loans/payment', [StaffController::class, 'recordLoanPayment'])->name('loans.payment');

        // Product inventory (Admin)
        Route::get('/products/export', [StaffController::class, 'exportProducts'])->name('products.export');
        Route::post('/products/bulk-delete', [StaffController::class, 'bulkDeleteProducts'])->name('products.bulk-delete');
        Route::get('/products', [StaffController::class, 'products'])->name('products');
        Route::post('/products', [StaffController::class, 'storeProduct'])->name('products.store');
        Route::post('/products/{id}/update', [StaffController::class, 'updateProduct'])->name('products.update');
        Route::post('/products/{id}/update-stock', [StaffController::class, 'updateProductStockInline'])->name('products.update-stock');
        Route::post('/products/{id}/delete', [StaffController::class, 'deleteProduct'])->name('products.delete');

        // Member management (Admin/Staff)
        Route::get('/members/export', [StaffController::class, 'exportMembers'])->name('members.export');
        Route::post('/members/bulk-delete', [StaffController::class, 'bulkDeleteMembers'])->name('members.bulk-delete');
        Route::get('/members', [StaffController::class, 'members'])->name('members');
        Route::post('/members', [StaffController::class, 'storeMember'])->name('members.store');
        Route::post('/members/{id}/update', [StaffController::class, 'updateMember'])->name('members.update');
        Route::post('/members/{id}/delete', [StaffController::class, 'deleteMember'])->name('members.delete');

        // SHU Dividend Calculator
        Route::get('/shu', [StaffController::class, 'shu'])->name('shu');
    });
});
