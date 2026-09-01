<?php

use Illuminate\Support\Facades\Route;

//---controllers----
use App\Http\Controllers\Auth\CheckoutController;

//---Auth CONTROLLERS LINKS---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterTenantController;
use App\Http\Controllers\HomeController;

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
use App\Http\Controllers\Admin\Branch\BranchPaymentGatewayController;
use App\Http\Controllers\Admin\Settings\MenuSettingsController;
use App\Http\Controllers\Admin\Employee\EmployeeController;
use App\Http\Controllers\Modules\Table\TableController;

//----Admin Menu Management Controllers Links---
use App\Http\Controllers\Modules\MenuManagement\CategoryController;
use App\Http\Controllers\Modules\MenuManagement\ItemController;
use App\Http\Controllers\Modules\MediaLibrary\MediaLibraryController;
use App\Http\Controllers\Modules\Orders\PosController;
use App\Http\Controllers\Modules\Orders\OrderHistoryController;
use App\Http\Controllers\Admin\Billing\BillingDraftController;
use App\Http\Controllers\Admin\Billing\BillingCheckoutController;
use App\Http\Controllers\Modules\PublicMenu\PublicMenuController;
use App\Http\Controllers\Modules\PublicMenu\OrderStatusController;
use App\Http\Controllers\Modules\Orders\OrderController;
use App\Http\Controllers\Modules\Orders\OrderItemActionController;
use App\Models\Order;
use App\Models\KotPrintLog;
//-------------------------Modules Controllers----------------------
use App\Http\Controllers\Modules\Kds\KdsController;
// =====================================================================================================

// --- AUTH ROUTES ---
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- PUBLIC ROUTES ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/menu/scan/{qr_token}', [PublicMenuController::class, 'showByQr'])
    ->name('public.menu.scan');
Route::get('/menu/{tenant_slug}/{branch_id?}', [PublicMenuController::class, 'show'])
    ->name('public.menu.show');

Route::post('/place-order', [OrderController::class, 'store'])->name('order.store');
Route::post('/call-waiter', [PublicMenuController::class, 'callWaiter'])->name('waiter.call');
Route::post('/request-bill', [PublicMenuController::class, 'requestBill'])->name('bill.request');
Route::get('/table/{qr_token}/order-status', [OrderStatusController::class, 'orderStatus'])
    ->name('public.order.status');
Route::get('/table/{qr_token}/order-status/pdf', [OrderStatusController::class, 'orderStatusPdf'])
    ->name('public.order.status.pdf');
Route::post('/table/{qr_token}/payment/initiate', [OrderStatusController::class, 'initiatePayment'])
    ->name('public.order.payment.initiate');
Route::match(['GET', 'POST'], '/table/{qr_token}/payment/return', [OrderStatusController::class, 'paymentReturn'])
    ->name('public.order.payment.return');
Route::get('/table/{qr_token}/thank-you', [OrderStatusController::class, 'thankYou'])
    ->name('public.order.thank-you');
Route::view('/ui/order-flow-demo', 'core.components.order-flow.index')
    ->name('demo.order-flow');
Route::view('/ui/order-flow', 'core.components.order-flow.index')
    ->name('ui.order-flow');


Route::get('/admin/get-table-orders/{table_number}', function ($tableNumber) {
    $tenantId = auth()->user()->tenant_id;
    $requestedBranchId = request()->filled('branch_id') ? (int) request()->query('branch_id') : null;

    $ordersQuery = Order::where('tenant_id', $tenantId)
        ->where('table_number', $tableNumber)
        // Active table drawer should show currently running orders
        ->where('status', 'running')
        ->when($requestedBranchId, function ($query) use ($requestedBranchId) {
            $query->where('branch_id', $requestedBranchId);
        })
        ->with(['items.orderItemAddons.masterAddon', 'items.creator', 'creator'])
        ->latest();

    $orders = $ordersQuery->get();

    $kotPrintCounts = KotPrintLog::query()
        ->selectRaw('kot_number, COUNT(*) as print_count, MAX(created_at) as last_printed_at')
        ->where('tenant_id', $tenantId)
        ->where('table_number', $tableNumber)
        ->when($requestedBranchId, function ($query) use ($requestedBranchId) {
            $query->where('branch_id', $requestedBranchId);
        })
        ->groupBy('kot_number')
        ->get()
        ->keyBy(fn ($row) => (int) $row->kot_number);

    return response()->json($orders->map(function ($order) use ($kotPrintCounts) {
        $items = $order->items->map(function ($item) use ($kotPrintCounts) {
            $kotNumber = (int) ($item->kot_number ?? 0);
            $printLog = $kotNumber > 0 ? $kotPrintCounts->get($kotNumber) : null;
            $printCount = (int) ($printLog->print_count ?? 0);
            $lastPrintedAt = $printLog->last_printed_at ?? null;

            $item->setAttribute('kot_print_count', $printCount);
            $item->setAttribute('kot_is_printed', $printCount > 0);
            $item->setAttribute('kot_last_printed_at', $lastPrintedAt ? (string) $lastPrintedAt : null);

            return $item;
        });

        $order->setRelation('items', $items);
        $data = $order->toArray();
        $data['ordered_at_iso'] = optional($order->ordered_at)->toIso8601String();

        return $data;
    })->values());
})->name('admin.tables.get_orders');

//------CHECKOUT ROUTE------
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout/send-otp', [CheckoutController::class, 'sendOtp'])->name('checkout.otp.send');
Route::post('/checkout/verify-otp', [CheckoutController::class, 'verifyOtp'])->name('checkout.otp.verify');
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
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('admin.branches.update');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('admin.branches.destroy');
        Route::get('/branches/payment-gateways', [BranchPaymentGatewayController::class, 'index'])->name('admin.branches.payment-gateways');
        Route::post('/branches/payment-gateways', [BranchPaymentGatewayController::class, 'store'])->name('admin.branches.payment-gateways.store');
        Route::delete('/branches/payment-gateways/{config}', [BranchPaymentGatewayController::class, 'destroy'])->name('admin.branches.payment-gateways.destroy');
        Route::get('/settings/menu', [MenuSettingsController::class, 'index'])->name('admin.settings.menu.index');
        Route::put('/settings/menu/{branch}', [MenuSettingsController::class, 'update'])->name('admin.settings.menu.update');



        Route::get('/employee', [EmployeeController::class, 'index'])->name('admin.employee.index');
        Route::post('/employee/store', [EmployeeController::class, 'store'])->name('admin.employee.store');
        Route::put('/employee/{employee}', [EmployeeController::class, 'update'])->name('admin.employee.update');
        Route::delete('/employee/{employee}', [EmployeeController::class, 'destroy'])->name('admin.employee.destroy');

        //Menu Managemnt
        Route::get('/menu/item', [ItemController::class, 'index'])->name('menu.items');
        Route::get('/media-library', [MediaLibraryController::class, 'index'])
            ->middleware('check.service:media-library')
            ->name('admin.media-library.index');


        Route::get('/menu/preview', [PublicMenuController::class, 'showAdmin'])->name('menu.preview');
        Route::get('/table/order-status', [OrderStatusController::class, 'orderStatus'])
            ->name('admin.order.status');


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
        Route::get('/orders/history', [OrderHistoryController::class, 'index'])->name('admin.orders.history');
        Route::get('/orders/history/export', [OrderHistoryController::class, 'export'])->name('admin.orders.history.export');
        Route::post('/order-items/{id}/serve', [OrderItemActionController::class, 'serve'])->name('admin.order-items.serve');
        Route::post('/order-items/{id}/cancel', [OrderItemActionController::class, 'cancel'])->name('admin.order-items.cancel');
        // -----------------------SERVICES-------------------

        // Table
        Route::get('/tables', [TableController::class, 'index'])->name('admin.tables.index');
        Route::get('/tables/{table_number}/kot/pdf', [TableController::class, 'kotPdf'])->name('admin.tables.kot.pdf');
        Route::post('/tables/bulk', [TableController::class, 'bulkStore'])->name('admin.tables.bulk-store');
        Route::put('/tables/{id}', [TableController::class, 'update'])->name('admin.tables.update');
        Route::get('/orders/manual', [PosController::class, 'index'])->name('admin.order.index');
        Route::get('/billing/drafts/{table}', [BillingDraftController::class, 'show'])->name('admin.billing.drafts.show');
        Route::post('/billing/drafts', [BillingDraftController::class, 'store'])->name('admin.billing.drafts.store');
        Route::delete('/billing/drafts/{table}', [BillingDraftController::class, 'destroy'])->name('admin.billing.drafts.destroy');
        Route::post('/billing/checkout', [BillingCheckoutController::class, 'store'])->name('admin.billing.checkout.store');
        Route::post('/billing/estimate/pdf', [BillingCheckoutController::class, 'estimatePdf'])->name('admin.billing.estimate.pdf');

        // Route::middleware(['role:admin,manager,sales_manager'])->prefix('billing')->group(function () {
        //     Route::get('/preview', function () {
        //         return view('modules.billing.pos');
        //     })->name('billing.preview');
        // });



        // Route::middleware(['role:admin,manager,sales_manager', 'check.service:table'])->prefix('table')->group(function () {
        //     Route::get('/', function () {
        //         return view('services.table.index');
        //     })->name('table.index');
        // });



        // A. Billing
        // Route::middleware(['role:admin,manager,sales_manager', 'check.service:billing'])->prefix('billing')->group(function () {
        //     Route::get('/', function () {
        //         return view('modules.billing.pos');
        //     })->name('billing.index');
        // });

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
        Route::get('/', [PosController::class, 'index'])->name('order.index');
    });
});
