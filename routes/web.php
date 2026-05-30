<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\StaffController;

// =====================================================================
// ROOT: Redirect to guest storefront (no login required)
// =====================================================================
Route::redirect('/', '/catalog');

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
// URL: /login, /register
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// =====================================================================
// PUBLIC STOREFRONT — Semua warga bisa browse tanpa login
// =====================================================================
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/product/{id}', [CatalogController::class, 'show'])->name('catalog.show');

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
        Route::post('/savings/deposit', [MemberController::class, 'depositSaving'])->name('savings.deposit');
        Route::get('/loans', [MemberController::class, 'loans'])->name('loans');
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

        // Order management (Kasir)
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
        Route::get('/products', [StaffController::class, 'products'])->name('products');
        Route::post('/products', [StaffController::class, 'storeProduct'])->name('products.store');
        Route::post('/products/{id}/update', [StaffController::class, 'updateProduct'])->name('products.update');
        Route::post('/products/{id}/delete', [StaffController::class, 'deleteProduct'])->name('products.delete');

        // SHU Dividend Calculator
        Route::get('/shu', [StaffController::class, 'shu'])->name('shu');
    });
});
