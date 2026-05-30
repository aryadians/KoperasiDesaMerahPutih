<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\StaffController;

// Welcome Page / Catalog
Route::redirect('/', '/catalog');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Public Catalog Routes (Access for regular guest buyers)
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/product/{id}', [CatalogController::class, 'show'])->name('catalog.show');

// Public Cart Routes (Guests can add items to cart)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Central dashboard routing
    Route::get('/dashboard', function () {
        if (auth()->user()->role === 'anggota') {
            return redirect()->route('member.dashboard');
        }
        return redirect()->route('staff.dashboard');
    })->name('dashboard');

    // Checkout requires auth
    Route::post('/cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');

    // Member Specific Routes
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

    // Orders Views
    Route::get('/orders/{id}', [MemberController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{id}/pay', [MemberController::class, 'payOrder'])->name('orders.pay');
    Route::post('/orders/{id}/cancel', [MemberController::class, 'cancelOrder'])->name('orders.cancel');

    // Staff / Management Specific Routes
    Route::prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');
        
        // Cashier routes
        Route::get('/orders', [StaffController::class, 'orders'])->name('orders');
        Route::post('/orders/{id}/{status}', [StaffController::class, 'updateOrderStatus'])->name('orders.update');
        Route::get('/crops', [StaffController::class, 'crops'])->name('crops');
        Route::post('/crops/{id}/{status}', [StaffController::class, 'updateCropStatus'])->name('crops.update');
        
        // Loan Underwriting & Repayment
        Route::get('/loans', [StaffController::class, 'loans'])->name('loans');
        Route::post('/loans/{id}/{status}', [StaffController::class, 'updateLoanStatus'])->name('loans.update');
        Route::post('/loans/payment', [StaffController::class, 'recordLoanPayment'])->name('loans.payment');
        
        // Product inventory
        Route::get('/products', [StaffController::class, 'products'])->name('products');
        Route::post('/products', [StaffController::class, 'storeProduct'])->name('products.store');
        Route::post('/products/{id}/update', [StaffController::class, 'updateProduct'])->name('products.update');
        Route::post('/products/{id}/delete', [StaffController::class, 'deleteProduct'])->name('products.delete');
        
        // SHU Dividend Calculator
        Route::get('/shu', [StaffController::class, 'shu'])->name('shu');
    });
});
