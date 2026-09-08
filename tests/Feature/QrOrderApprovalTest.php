<?php

namespace Tests\Feature;

use App\Events\NewOrderReceived;
use App\Events\QrOrderSubmissionUpdated;
use App\Http\Controllers\Admin\QrOrderSubmissionController;
use App\Http\Controllers\Modules\Orders\OrderController;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\QrOrderSubmission;
use App\Models\TableAccessSession;
use App\Models\User;
use App\Services\PublicMenu\TableAccessSessionService;
use App\Services\QrOrderSubmissionService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\TestCase;

class QrOrderApprovalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'qr_test', 'database.connections.qr_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '', 'foreign_key_constraints' => false,
        ]]);
        $definitions = [
            'tenants' => ['company_name'], 'users' => ['tenant_id', 'branch_id', 'name', 'role'],
            'branches' => ['tenant_id', 'branch_name', 'tax_setting', 'tax_rate', 'latitude', 'longitude', 'country_id', 'currency_id', 'deleted_at'],
            'tables' => ['tenant_id', 'branch_id', 'table_number', 'qr_token', 'status', 'is_active'],
            'table_access_sessions' => ['tenant_id', 'branch_id', 'table_id', 'session_token', 'status', 'expires_at', 'grace_expires_at'],
            'menu_items' => ['tenant_id', 'branch_id', 'name', 'base_price', 'sale_price', 'is_available', 'is_active', 'has_variants', 'deleted_at'],
            'menu_item_variants' => ['menu_item_id', 'name', 'base_price', 'sale_price', 'is_available'],
            'menu_item_addons' => ['menu_item_id', 'name', 'price', 'is_active'],
            'orders' => ['tenant_id', 'branch_id', 'table_id', 'order_number', 'table_number', 'order_type', 'subtotal', 'discount_amount', 'tax_amount', 'grand_total', 'status', 'payment_status', 'notes', 'source', 'created_by', 'ordered_at', 'kitchen_status'],
            'order_items' => ['order_id', 'source', 'created_by', 'kot_number', 'menu_item_id', 'menu_item_variant_id', 'item_name', 'price', 'quantity', 'total', 'notes', 'status', 'invoice_id'],
            'order_item_addons' => ['order_item_id', 'menu_item_addon_id', 'addon_name', 'price', 'quantity'],
            'order_invoices' => ['order_id', 'tenant_id', 'branch_id', 'status', 'grand_total'],
        ];
        foreach ($definitions as $name => $columns) {
            Schema::create($name, function (Blueprint $table) use ($columns) {
                $table->id();
                foreach ($columns as $column) {
                    if (str_ends_with($column, '_id') || in_array($column, ['created_by', 'kot_number', 'quantity'])) {
                        $table->integer($column)->nullable();
                    } elseif (in_array($column, ['price', 'base_price', 'sale_price', 'total', 'subtotal', 'grand_total', 'tax_amount', 'discount_amount', 'tax_rate'])) {
                        $table->decimal($column, 12, 2)->nullable();
                    } else {
                        $table->string($column)->nullable();
                    }
                }
                $table->timestamps();
            });
        }
        (require database_path('migrations/2026_09_07_140000_create_qr_order_submissions.php'))->up();
        DB::table('branches')->insert(['id' => 1, 'tenant_id' => 1, 'branch_name' => 'Main', 'tax_setting' => 'exclusive', 'tax_rate' => 10]);
        DB::table('tables')->insert(['id' => 1, 'tenant_id' => 1, 'branch_id' => 1, 'table_number' => 'T-03', 'qr_token' => 'QR-TEST', 'status' => 'available', 'is_active' => '1']);
        DB::table('table_access_sessions')->insert(['id' => 1, 'tenant_id' => 1, 'branch_id' => 1, 'table_id' => 1, 'session_token' => 'session', 'status' => 'active', 'expires_at' => now()->addHour()]);
        DB::table('menu_items')->insert(['id' => 1, 'tenant_id' => 1, 'branch_id' => 1, 'name' => 'Coffee', 'base_price' => 100, 'is_available' => '1', 'is_active' => '1', 'has_variants' => '0']);
        Event::fake([NewOrderReceived::class, QrOrderSubmissionUpdated::class]);
        $sessions = \Mockery::mock(TableAccessSessionService::class)->makePartial();
        $sessions->shouldReceive('getLatestSessionForTable')->andReturn(TableAccessSession::find(1));
        $sessions->shouldReceive('findValidSessionForTable')->andReturnUsing(fn ($table, $token) => $token === 'session' ? TableAccessSession::find(1) : null);
        $sessions->shouldReceive('touchSession')->andReturn(TableAccessSession::find(1));
        $this->app->instance(TableAccessSessionService::class, $sessions);
    }

    private function place(array $extra = []): array
    {
        $request = Request::create('/place-order', 'POST', array_replace([
            'items' => [['id' => 1, 'name' => 'Tampered name', 'price' => 1, 'quantity' => 2, 'unique_key' => '1_0_0_none']],
            'table_id' => 1, 'session_token' => 'session', 'order_type' => 'dine_in', 'source' => 'qr', 'request_key' => (string) Str::uuid(),
        ], $extra));

        return app(OrderController::class)->store($request)->getData(true);
    }

    private function staff(string $role = 'admin', int $branch = 1, int $tenant = 1): User
    {
        $user = new User(['role' => $role, 'branch_id' => $branch, 'tenant_id' => $tenant]);
        $user->id = 10;
        Auth::setUser($user);
        session(['active_branch_id' => $branch]);

        return $user;
    }

    public function test_qr_settings_use_admin_selected_branch_and_manager_assigned_branch(): void
    {
        DB::table('branches')->insert(['id' => 2, 'tenant_id' => 1, 'branch_name' => 'Second']);
        $controller = app(\App\Http\Controllers\Admin\Settings\QrOrderSettingsController::class);
        foreach (['admin' => 2, 'manager' => 1] as $role => $expectedBranch) {
            $user = $this->staff($role);
            session(['active_branch_id' => 2]);
            $request = Request::create('/admin/settings/qr-orders', 'PUT', [
                'branch_id' => $expectedBranch, 'auto_accept_qr_orders' => '1',
            ]);
            $request->setUserResolver(fn () => $user);
            $request->setLaravelSession(app('session.store'));
            $this->assertSame($expectedBranch, $controller->index($request)->getData()['branch']->id);
            $controller->update($request);
            $this->assertTrue(Branch::find($expectedBranch)->auto_accept_qr_orders);
            $request->merge(['auto_accept_qr_orders' => '0']);
            $request->headers->set('Accept', 'application/json');
            $response = $controller->update($request);
            $this->assertFalse($response->getData(true)['auto_accept_qr_orders']);
            $this->assertSame(200, $response->getStatusCode());
            $this->assertFalse(Branch::find($expectedBranch)->auto_accept_qr_orders);
        }
    }

    public function test_qr_settings_cannot_target_another_branch(): void
    {
        $user = $this->staff('manager');
        $request = Request::create('/admin/settings/qr-orders', 'PUT', ['branch_id' => 2, 'auto_accept_qr_orders' => 1]);
        $request->setUserResolver(fn () => $user);
        $request->setLaravelSession(app('session.store'));
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(\App\Http\Controllers\Admin\Settings\QrOrderSettingsController::class)->update($request);
    }

    public function test_guest_cannot_bypass_confirmation_by_claiming_waiter_source_and_retry_is_idempotent(): void
    {
        $key = (string) Str::uuid();
        $first = $this->place(['source' => 'waiter', 'request_key' => $key]);
        $second = $this->place(['source' => 'waiter', 'request_key' => $key]);
        $this->assertTrue($first['pending_confirmation']);
        $this->assertSame($first['submission_id'], $second['submission_id']);
        $this->assertSame(1, QrOrderSubmission::count());
        $this->assertSame(0, Order::count());
        $this->assertSame(0, OrderItem::count());
        $this->assertSame(0, DB::table('order_invoices')->count());
        $this->assertSame('220.00', QrOrderSubmission::first()->total);
        Event::assertNotDispatched(NewOrderReceived::class);
    }

    public function test_accept_creates_one_kot_and_repeat_accept_does_not_duplicate(): void
    {
        $this->place();
        $user = $this->staff();
        $service = app(QrOrderSubmissionService::class);
        $accepted = $service->handle(QrOrderSubmission::first(), $user, true);
        $again = $service->handle($accepted, $user, true);
        $this->assertSame('accepted', $accepted->status);
        $this->assertSame($accepted->kot_number, $again->kot_number);
        $this->assertSame(1, Order::count());
        $this->assertSame(1, OrderItem::count());
        $this->assertSame(220.0, (float) Order::first()->grand_total);
        $this->assertSame('qr', OrderItem::first()->source);
        $this->assertNull(OrderItem::first()->created_by);
        Event::assertDispatchedTimes(NewOrderReceived::class, 1);
    }

    public function test_waiter_order_remains_direct_and_pending_addition_does_not_change_its_totals(): void
    {
        $this->staff('waiter');
        $placed = $this->place(['source' => 'waiter', 'session_token' => null]);
        $this->assertTrue($placed['success']);
        $total = Order::first()->grand_total;
        Auth::forgetGuards();
        $this->place();
        $this->assertSame(1, OrderItem::count());
        $this->assertEquals($total, Order::first()->grand_total);
        $service = app(QrOrderSubmissionService::class);
        $service->handle(QrOrderSubmission::first(), $this->staff('manager'), true);
        $this->assertSame(1, Order::count());
        $this->assertSame(2, OrderItem::count());
        $this->assertSame(2, OrderItem::distinct()->count('kot_number'));
    }

    public function test_auto_accept_goes_direct_and_rejection_never_creates_kitchen_items(): void
    {
        Branch::find(1)->update(['auto_accept_qr_orders' => true]);
        $result = $this->place();
        $this->assertFalse($result['pending_confirmation']);
        $accepted = QrOrderSubmission::firstOrFail();
        $receipt = Request::create('/qr-order-status/'.$accepted->public_token.'?placed=1');
        $redirect = app(QrOrderSubmissionController::class)->status($receipt, $accepted->public_token);
        $this->assertStringContainsString('placed=1', $redirect->getTargetUrl());
        $this->assertSame(1, OrderItem::count());
        Branch::find(1)->update(['auto_accept_qr_orders' => false]);
        $this->place();
        $pending = QrOrderSubmission::where('status', 'pending')->firstOrFail();
        app(QrOrderSubmissionService::class)->handle($pending, $this->staff(), false, 'Unavailable');
        $this->assertSame('rejected', $pending->fresh()->status);
        $this->assertSame(1, OrderItem::count());
    }

    public function test_another_branch_cannot_accept_and_chef_has_no_approval_access(): void
    {
        $this->place();
        $user = $this->staff('manager', 2);
        $request = Request::create('/qr-order-approvals/1', 'POST', ['action' => 'accept']);
        $request->setUserResolver(fn () => $user);
        try {
            app(QrOrderSubmissionController::class)->handle($request, QrOrderSubmission::first(), app(QrOrderSubmissionService::class));
            $this->fail('Cross branch acceptance must be denied');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            $this->assertSame(403, $exception->getStatusCode());
        }
        $authorize = \Illuminate\Support\Facades\Broadcast::getChannels()['qr-approvals.branch.{branchId}'];
        $this->assertFalse($authorize($this->staff('chef'), 1));
        $this->assertFalse($authorize($this->staff('admin', 1, 2), 1));
    }

    public function test_unavailable_item_and_expired_session_cannot_be_accepted(): void
    {
        $this->place();
        DB::table('menu_items')->where('id', 1)->update(['is_available' => '0']);
        try {
            app(QrOrderSubmissionService::class)->handle(QrOrderSubmission::first(), $this->staff(), true);
            $this->fail('Unavailable item was accepted');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertSame(0, OrderItem::count());
            $this->assertSame('pending', QrOrderSubmission::first()->status);
        }
        DB::table('menu_items')->update(['is_available' => '1']);
        DB::table('table_access_sessions')->update(['status' => 'grace']);
        $this->expectException(\Illuminate\Validation\ValidationException::class);
        app(QrOrderSubmissionService::class)->handle(QrOrderSubmission::first(), $this->staff(), true);
    }

    public function test_pending_notifications_restore_from_database_and_customer_page_renders(): void
    {
        $this->place();
        $user = $this->staff('waiter');
        $request = Request::create('/qr-order-approvals');
        $request->setUserResolver(fn () => $user);
        $controller = app(QrOrderSubmissionController::class);
        $rows = $controller->index($request)->getData(true);
        $this->assertCount(1, $rows);
        $this->assertSame('Coffee', $rows[0]['items'][0]['name']);
        $submission = QrOrderSubmission::first();
        $view = $controller->status(Request::create('/qr-order-status/'.$submission->public_token), $submission->public_token);
        $html = $view->render();
        $this->assertStringContainsString('Awaiting restaurant confirmation', $html);
        $this->assertStringContainsString('Coffee', $html);
        $this->assertStringNotContainsString('Order Placed Successfully!', $html);
        $placedRequest = Request::create('/qr-order-status/'.$submission->public_token.'?placed=1');
        $this->assertStringContainsString('Order Placed Successfully!', $controller->status($placedRequest, $submission->public_token)->render());
        $service = app(QrOrderSubmissionService::class);
        $service->handle($submission, $user, false, 'Sold out');
        $this->assertCount(0, $controller->index($request)->getData(true));
        $jsonRequest = Request::create('/qr-order-status/'.$submission->public_token);
        $jsonRequest->headers->set('Accept', 'application/json');
        $result = $controller->status($jsonRequest, $submission->public_token)->getData(true);
        $this->assertSame('rejected', $result['status']);
        $this->assertSame('Sold out', $result['reason']);
        $this->assertNull($result['redirect_url']);
    }

    public function test_acceptance_failure_rolls_back_without_poisoning_retry(): void
    {
        $this->place();
        $realController = app(OrderController::class);
        $broken = \Mockery::mock(OrderController::class);
        $broken->shouldReceive('store')->once()->andReturn(response()->json(['success' => false], 500));
        $this->app->instance(OrderController::class, $broken);
        $user = $this->staff();
        try {
            app(QrOrderSubmissionService::class)->handle(QrOrderSubmission::first(), $user, true);
            $this->fail('Failure should be propagated');
        } catch (\Illuminate\Validation\ValidationException $exception) {
            $this->assertSame('pending', QrOrderSubmission::first()->status);
            $this->assertSame(0, DB::transactionLevel());
            Event::assertNotDispatched(NewOrderReceived::class);
        }
        $this->app->instance(OrderController::class, $realController);
        $this->assertSame('accepted', app(QrOrderSubmissionService::class)->handle(QrOrderSubmission::first(), $user, true)->status);
    }
}
