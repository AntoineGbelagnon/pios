<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\StatisticController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\WarrantyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check() ? redirect()->route('dashboard') : app(AuthController::class)->create();
});

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/two-factor-challenge', [AuthController::class, 'createTwoFactor'])->name('two-factor.create');
    Route::post('/two-factor-challenge', [AuthController::class, 'verifyTwoFactor'])->name('two-factor.verify');
    Route::post('/two-factor-challenge/resend', [AuthController::class, 'resendTwoFactor'])->name('two-factor.resend');
});

Route::middleware(['auth', 'company.team'])->group(function (): void {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::middleware('can:manage users')->group(function (): void {
        Route::get('/dashboard/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/dashboard/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/dashboard/users', [UserController::class, 'store'])->name('users.store');
        Route::patch('/dashboard/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });

    Route::middleware('can:manage settings')->group(function (): void {
        Route::get('/dashboard/settings', [AdminController::class, 'settings'])->name('settings.index');
        Route::put('/dashboard/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::resource('dashboard/shops', ShopController::class)->names('shops')->except(['show', 'destroy']);
        Route::patch('/dashboard/shops/{shop}/toggle', [ShopController::class, 'toggle'])->name('shops.toggle');
    });

    Route::middleware('can:view audit log')->group(function (): void {
        Route::get('/dashboard/activity', [AdminController::class, 'activity'])->name('activity.index');
    });

    Route::middleware('can:manage stocks')->group(function (): void {
        Route::resource('dashboard/warehouses', WarehouseController::class)->names('warehouses')->except(['show', 'destroy']);
        Route::prefix('dashboard/stock')->name('stock.')->group(function (): void {
            Route::get('/', [StockController::class, 'index'])->name('index');
            Route::get('/movements', [StockController::class, 'movements'])->name('movements');
            Route::get('/movements/create', [StockController::class, 'createMovement'])->name('movement.create');
            Route::post('/movements', [StockController::class, 'storeMovement'])->name('movement.store');
            Route::get('/adjustment', [StockController::class, 'createAdjustment'])->name('adjustment.create');
            Route::post('/adjustment', [StockController::class, 'storeAdjustment'])->name('adjustment.store');
            Route::get('/low', [StockController::class, 'lowStock'])->name('low');
            Route::get('/inventory', [StockController::class, 'inventory'])->name('inventory');
            Route::post('/inventory', [StockController::class, 'storeInventory'])->name('inventory.store');
        });
    });

    Route::middleware('can:manage products')->group(function (): void {
        Route::resource('dashboard/categories', CategoryController::class)->names('categories')->except(['show']);
        Route::resource('dashboard/brands', BrandController::class)->names('brands')->except(['show']);
        Route::resource('dashboard/products', ProductController::class)->names('products')->except(['show']);
    });

    Route::middleware('can:manage customers')->group(function (): void {
        Route::resource('dashboard/customers', CustomerController::class)->names('customers')->except(['show']);
        Route::patch('/dashboard/customers/{customer}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');
    });

    Route::middleware('can:manage invoices')->group(function (): void {
        Route::resource('dashboard/suppliers', SupplierController::class)->names('suppliers')->except(['show']);
        Route::patch('/dashboard/suppliers/{supplier}/toggle', [SupplierController::class, 'toggle'])->name('suppliers.toggle');
    });

    Route::middleware('can:manage sales')->group(function (): void {
        Route::get('/dashboard/sales', [SaleController::class, 'index'])->name('sales.index');
        Route::get('/dashboard/sales/pos', [SaleController::class, 'pos'])->name('sales.pos');
        Route::get('/dashboard/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
        Route::post('/dashboard/sales', [SaleController::class, 'store'])->name('sales.store');
        Route::patch('/dashboard/sales/{sale}/cancel', [SaleController::class, 'cancel'])->name('sales.cancel');
        Route::get('/dashboard/sales/{sale}/receipt', [SaleController::class, 'receipt'])->name('sales.receipt');
        Route::get('/api/products/search', [SaleController::class, 'searchProducts'])->name('products.search');
    });

    Route::middleware('can:manage cash')->group(function (): void {
        Route::get('/dashboard/cash', [CashRegisterController::class, 'index'])->name('cash_registers.index');
        Route::get('/dashboard/cash/create', [CashRegisterController::class, 'create'])->name('cash_registers.create');
        Route::post('/dashboard/cash', [CashRegisterController::class, 'store'])->name('cash_registers.store');
        Route::get('/dashboard/cash/{cash_register}', [CashRegisterController::class, 'show'])->name('cash_registers.show');
        Route::patch('/dashboard/cash/{cash_register}/close', [CashRegisterController::class, 'close'])->name('cash_registers.close');
    });

    Route::middleware('can:manage expenses')->group(function (): void {
        Route::get('/dashboard/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::get('/dashboard/expenses/create', [ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/dashboard/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::delete('/dashboard/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    Route::middleware('can:manage purchases')->group(function (): void {
        Route::get('/dashboard/purchases', [PurchaseController::class, 'index'])->name('purchases.index');
        Route::get('/dashboard/purchases/create', [PurchaseController::class, 'create'])->name('purchases.create');
        Route::post('/dashboard/purchases', [PurchaseController::class, 'store'])->name('purchases.store');
        Route::get('/dashboard/purchases/{purchase}', [PurchaseController::class, 'show'])->name('purchases.show');
        Route::patch('/dashboard/purchases/{purchase}/receive', [PurchaseController::class, 'receive'])->name('purchases.receive');
    });

    Route::middleware('can:manage returns')->group(function (): void {
        Route::get('/dashboard/returns', [ReturnController::class, 'index'])->name('returns.index');
        Route::get('/dashboard/returns/create', [ReturnController::class, 'create'])->name('returns.create');
        Route::post('/dashboard/returns', [ReturnController::class, 'store'])->name('returns.store');
        Route::get('/dashboard/returns/{return}', [ReturnController::class, 'show'])->name('returns.show');
    });

    Route::middleware('can:manage warranties')->group(function (): void {
        Route::get('/dashboard/warranties', [WarrantyController::class, 'index'])->name('warranties.index');
        Route::get('/dashboard/warranties/create', [WarrantyController::class, 'create'])->name('warranties.create');
        Route::post('/dashboard/warranties', [WarrantyController::class, 'store'])->name('warranties.store');
        Route::get('/dashboard/warranties/{warranty}', [WarrantyController::class, 'show'])->name('warranties.show');
        Route::patch('/dashboard/warranties/{warranty}', [WarrantyController::class, 'update'])->name('warranties.update');
    });

    Route::middleware('can:manage settings')->group(function (): void {
        Route::get('/dashboard/statistics', [StatisticController::class, 'index'])->name('statistics.index');
        Route::get('/dashboard/statistics/export/sales', [StatisticController::class, 'exportSales'])->name('statistics.export.sales');
        Route::get('/dashboard/statistics/export/products', [StatisticController::class, 'exportProducts'])->name('statistics.export.products');
        Route::get('/dashboard/statistics/export/customers', [StatisticController::class, 'exportCustomers'])->name('statistics.export.customers');
    });

    Route::get('/dashboard/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('/dashboard/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/dashboard/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});
