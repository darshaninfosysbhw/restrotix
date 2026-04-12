<?php

use Illuminate\Support\Facades\Route;

//---controllers----
use App\Http\Controllers\CheckoutController;

//---Auth CONTROLLERS LINKS---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterTenantController;

//---Super Admin CONTROLLERS LINKS---
use App\Http\Controllers\SuperAdmin\TenantController;
use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\ImpersonateController;
use App\Http\Controllers\SuperAdmin\SuperAdminProfileController;
use App\Http\Controllers\SuperAdmin\ServiceController;
use App\Http\Controllers\SuperAdmin\CurrencyController;
use App\Http\Controllers\SuperAdmin\PlanController;

//---ADMIN CONTROLLERS LINKS---
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\Branch\BranchController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Admin\Table\TableController;

//----Admin Menu Management Controllers Links---
use App\Http\Controllers\Admin\MenuManagement\CategoryController;
use App\Http\Controllers\Admin\MenuManagement\ItemController;
use App\Http\Controllers\Admin\MenuManagement\PreviewController;
use App\Http\Controllers\Admin\Orders\OrderController;
use App\Http\Controllers\Admin\Orders\OrderItemActionController;
use App\Models\Order;
//-------------------------Modules Controllers----------------------
use App\Http\Controllers\Kds\KdsController;

// =====================================================================================================

// --- AUTH ROUTES ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- PUBLIC ROUTES ---
Route::get('/', [RegisterTenantController::class, 'index'])->name('home');
Route::get('/menu/scan/{qr_token}', [PreviewController::class, 'showByQr'])
    ->name('public.menu.scan');
Route::get('/menu/{tenant_slug}/{branch_id?}', [PreviewController::class, 'show'])
    ->name('public.menu.show');

Route::post('/place-order', [OrderController::class, 'store'])->name('order.store');
Route::post('/call-waiter', [PreviewController::class, 'callWaiter'])->name('waiter.call');


Route::get('/admin/get-table-orders/{table_number}', function ($tableNumber) {
    $tenantId = auth()->user()->tenant_id;

    $orders = Order::where('tenant_id', $tenantId)
        ->where('table_number', $tableNumber)
        // Active table drawer should show currently running orders
        ->where('status', 'running')
        ->with('items')
        ->latest()
        ->get();

    return response()->json($orders);
})->name('admin.tables.get_orders');

//------CHECKOUT ROUTE------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/about', function () {
    return view('about');
})->name('about');

Route::get('/billing', function () {
    return 'Please contact Super Admin to manage your subscription and billing details.';
})->name('admin.billing');

// --- AUTHENTICATED ROUTES ---
Route::middleware(['auth'])->group(function () {
    Route::get('/impersonate/leave', [ImpersonateController::class, 'leave'])->name('impersonate.leave');




    // 1. SUPER ADMIN PANEL (Global Control)
    Route::middleware(['role:superadmin'])->prefix('superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
        Route::post('/switch-country', [SuperAdminDashboardController::class, 'switchCountry'])->name('superadmin.switch.country');
        Route::get('/profile', [SuperAdminProfileController::class, 'show'])->name('superadmin.profile');
        Route::put('/profile', [SuperAdminProfileController::class, 'update'])->name('superadmin.profile.update');
        Route::put('/profile/password', [SuperAdminProfileController::class, 'updatePassword'])->name('superadmin.profile.password.update');

        Route::get('/tenants', [TenantController::class, 'index'])->name('superadmin.tenants.index');
        Route::post('/tenants/store', [TenantController::class, 'store'])->name('superadmin.tenants.store');
        Route::put('/tenants/{tenant}', [TenantController::class, 'update'])->name('superadmin.tenants.update');
        Route::delete('/tenants/{tenant}', [TenantController::class, 'destroy'])->name('superadmin.tenants.destroy');

        // (Optional) Impersonation Route for Super Admin to Access Direct Tenant Dashboards
        Route::get('/impersonate/user/{id}', [ImpersonateController::class, 'impersonate'])->name('impersonate');


        Route::get('/services', [ServiceController::class, 'index'])->name('superadmin.services.index');
        Route::post('/services', [ServiceController::class, 'store'])->name('superadmin.services.store');
        Route::put('/services/{service}', [ServiceController::class, 'update'])->name('superadmin.services.update');
        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('superadmin.services.destroy');

        Route::get('/currencies', [CurrencyController::class, 'index'])->name('superadmin.currencies.index');
        Route::post('/currencies', [CurrencyController::class, 'store'])->name('superadmin.currencies.store');
        Route::put('/currencies/{currency}', [CurrencyController::class, 'update'])->name('superadmin.currencies.update');
        Route::delete('/currencies/{currency}', [CurrencyController::class, 'destroy'])->name('superadmin.currencies.destroy');

        Route::get('/plans', [PlanController::class, 'index'])->name('superadmin.plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('superadmin.plans.store');
        Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('superadmin.plans.update');
        Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('superadmin.plans.destroy');

        Route::get('/paymentGateway', function () {
            return view('superadmin.master-settings.paymentGateway.index');
        })->name('superadmin.paymentGateway.index');
    });


    // 2. RESTAURANT ADMIN/STAFF PANEL (Branch Level)
    Route::prefix('admin')->middleware(['check.subscription'])->group(function () {

        //DASHBOARD & PROFILE
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/profile', [ProfileController::class, 'show'])->name('admin.profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('admin.profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('admin.profile.password.update');

        //Branches Routes
        Route::get('/branches', [BranchController::class, 'index'])->name('admin.branches.index');
        Route::post('/branches/store', [BranchController::class, 'store'])->name('admin.branches.store');



        Route::get('/employee', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::post('/employee/store', [EmployeeController::class, 'store'])->name('admin.employee.store');

        //Menu Managemnt
        Route::get('/menu/item', [ItemController::class, 'index'])->name('menu.items');


        Route::get('/menu/preview', [PreviewController::class, 'showAdmin'])->name('menu.preview');


        // Categories Routes
        Route::get('menu/categories', [CategoryController::class, 'index'])->name('admin.menu.categories.index');
        Route::post('menu/categories', [CategoryController::class, 'store'])->name('admin.menu.categories.store');
        Route::post('menu/categories/update/{id}', [CategoryController::class, 'update'])->name('admin.menu.categories.update');
        Route::patch('menu/categories/status/{id}', [CategoryController::class, 'toggleStatus'])->name('admin.menu.categories.toggle-status');
        Route::delete('menu/categories/delete/{id}', [CategoryController::class, 'destroy'])->name('admin.menu.categories.destroy');


        // 🍕 Menu Items (New)
        Route::get('menu/items', [ItemController::class, 'index'])->name('admin.menu.items.index');
        Route::post('menu/items', [ItemController::class, 'store'])->name('admin.menu.items.store');
        Route::post('menu/items/update/{id}', [ItemController::class, 'update'])->name('admin.menu.items.update');
        Route::patch('menu/items/status/{id}', [ItemController::class, 'toggleStatus'])->name('admin.menu.items.toggle-status');
        Route::delete('menu/items/delete/{id}', [ItemController::class, 'destroy'])->name('admin.menu.items.destroy');

        //-------------------------Temraory Zone--------------------
        // Route::middleware(['role:admin,manager,sales_manager,chef', 'check.service:billing'])->prefix('billing')->group(function () {
        //     Route::get('/kds', function () {
        //         return view('modules.kds.index');
        //     })->name('kds.index');
        // });

        Route::get('/waiter', function () {
            return view('waiter.index');
        })->name('admin.waiter.index');

        Route::get('/kds', [KdsController::class, 'index'])->name('admin.kds.index');
        Route::post('/kds/update-status/{id}', [KdsController::class, 'updateStatus'])->name('admin.kds.update-status');
        Route::post('/kds/mark-all-ready', [KdsController::class, 'markAllReady'])->name('admin.kds.mark-all-ready');
        Route::post('/kds/item/{id}/status', [KdsController::class, 'updateItemStatus'])->name('admin.kds.item-status');
        Route::post('/order-items/{id}/serve', [OrderItemActionController::class, 'serve'])->name('admin.order-items.serve');
        // -----------------------SERVICES-------------------

        // Table
        Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
        Route::post('/tables/bulk', [TableController::class, 'bulkStore'])->name('admin.tables.bulk-store');
        Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');
        Route::get('/orders/manual', [PreviewController::class, 'showWaiterOrderPanel'])->name('admin.order.index');



        // Route::middleware(['role:admin,manager,sales_manager', 'check.service:table'])->prefix('table')->group(function () {
        //     Route::get('/', function () {
        //         return view('services.table.index');
        //     })->name('table.index');
        // });



        // A. Billing
        Route::middleware(['role:admin,manager,sales_manager', 'check.service:billing'])->prefix('billing')->group(function () {
            Route::get('/', function () {
                return view('modules.billing.checkout');
            })->name('billing.index');
        });

        // B. Inventory & Marketplace
        Route::middleware(['role:admin,manager,purchase_manager,chef', 'check.service:inventory'])->group(function () {
            Route::prefix('inventory')->group(function () {
                Route::get('/', function () {
                    return view('modules.inventory.index');
                })->name('inventory.index');
            });

            Route::prefix('marketplace')->group(function () {
                Route::get('/', function () {
                    return view('modules.marketplace.browse');
                })->name('marketplace.index');
            });
        });

        // C. Accounts & Ledger
        Route::middleware(['role:admin,manager,account_manager', 'check.service:accounts'])->prefix('accounts')->group(function () {
            Route::get('/ledger', function () {
                return view('modules.accounts.ledger');
            })->name('accounts.ledger');
        });

        // D. Marketing
        Route::middleware(['role:admin,sales_manager', 'check.service:marketing'])->prefix('marketing')->group(function () {
            Route::get('/', function () {
                return view('modules.marketing.campaigns');
            })->name('marketing.index');
        });
    });


    // 🔥 WAITER ROUTES
    Route::prefix('waiter')->middleware(['auth', 'role:waiter'])->group(function () {
        Route::get('/tables', [TableController::class, 'index'])->name('waiter.tables.index');

        // ❗ waiter ke paas limited actions hone chahiye
        Route::get('/tables/{table}/orders', [TableController::class, 'getOrders'])->name('waiter.tables.orders');

        //
        Route::get('/', [PreviewController::class, 'showWaiterOrderPanel'])->name('order.index');
    });
});
