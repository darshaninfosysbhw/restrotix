<?php

namespace Tests\Feature;

use App\Services\Admin\DashboardService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class BranchPerformanceTest extends TestCase
{
    public function test_realtime_access_allows_owner_branches_but_blocks_other_tenants_and_staff_switching(): void
    {
        $authorize = \Illuminate\Support\Facades\Broadcast::getChannels()['orders.branch.{branchId}'];
        $admin = new \App\Models\User(['role' => 'admin', 'tenant_id' => 1, 'branch_id' => 1]);
        $this->assertTrue($authorize($admin, 1));
        $this->assertTrue($authorize($admin, 2));
        $this->assertFalse($authorize($admin, 3));
        $this->assertFalse($authorize($admin, 999));
        $this->assertFalse($authorize($admin, 0));
        foreach (['manager', 'chef', 'waiter'] as $role) {
            $staff = new \App\Models\User(['role' => $role, 'tenant_id' => 1, 'branch_id' => 2]);
            $this->assertTrue($authorize($staff, 2));
            $this->assertFalse($authorize($staff, 1));
            $this->assertFalse($authorize($staff, 3));
        }
        $admin->branch_id = null;
        $this->assertFalse($authorize($admin, 3));
        DB::table('branches')->where('id', 2)->update(['deleted_at' => now()]);
        $this->assertFalse($authorize($admin, 2));
    }

    private function dashboardUser(string $role = 'admin'): void
    {
        $user = new \App\Models\User(['role' => $role, 'tenant_id' => 1, 'branch_id' => 1]);
        $tenant = new \App\Models\Tenant();
        $tenant->setRelation('currency', new \App\Models\Currency(['symbol' => 'Rs ']));
        $user->setRelation('tenant', $tenant);
        \Illuminate\Support\Facades\Auth::setUser($user);
        session(['active_branch_id' => 1]);
    }

    public function test_owner_dashboard_defaults_to_all_without_changing_operational_branch(): void
    {
        $this->dashboardUser();
        $service = \Mockery::mock(DashboardService::class);
        $service->shouldReceive('buildDashboardPayload')->once()->with(1, 'Rs', null)->andReturn([]);
        $view = app(\App\Http\Controllers\Admin\DashboardController::class)->index(
            $service, \Illuminate\Http\Request::create('/admin/dashboard')
        );
        $this->assertSame('All Branches', $view->getData()['dashboardBranchLabel']);
        $this->assertNull($view->getData()['dashboardBranchId']);
        $this->assertCount(2, $view->getData()['dashboardBranches']);
        $this->assertSame(1, session('active_branch_id'));
    }

    public function test_owner_can_filter_dashboard_independently(): void
    {
        $this->dashboardUser();
        $service = \Mockery::mock(DashboardService::class);
        $service->shouldReceive('buildDashboardPayload')->once()->with(1, 'Rs', 2)->andReturn([]);
        $view = app(\App\Http\Controllers\Admin\DashboardController::class)->index(
            $service, \Illuminate\Http\Request::create('/admin/dashboard', 'GET', ['dashboard_branch' => '2'])
        );
        $this->assertSame('New', $view->getData()['dashboardBranchLabel']);
        $this->assertSame(1, session('active_branch_id'));
    }

    public function test_owner_cannot_select_another_tenants_branch(): void
    {
        $this->dashboardUser();
        $service = \Mockery::mock(DashboardService::class);
        $service->shouldNotReceive('buildDashboardPayload');
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(\App\Http\Controllers\Admin\DashboardController::class)->index(
            $service, \Illuminate\Http\Request::create('/admin/dashboard', 'GET', ['dashboard_branch' => '3'])
        );
    }

    public function test_manager_cannot_request_all_branches(): void
    {
        $this->dashboardUser('manager');
        $service = \Mockery::mock(DashboardService::class);
        $service->shouldReceive('buildDashboardPayload')->once()->with(1, 'Rs', 1)->andReturn([]);
        $view = app(\App\Http\Controllers\Admin\DashboardController::class)->index(
            $service, \Illuminate\Http\Request::create('/manager/dashboard', 'GET', ['dashboard_branch' => 'all'])
        );
        $this->assertSame(1, $view->getData()['dashboardBranchId']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'branch_test', 'database.connections.branch_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        $this->travelTo(now()->setDate(2026, 9, 15)->startOfDay());
        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->integer('tenant_id');
            $table->integer('country_id')->default(1);
            foreach (['branch_name', 'full_address', 'city', 'state', 'pincode', 'latitude', 'longitude'] as $field) {
                $table->string($field)->nullable();
            }
            $table->softDeletes();
        });
        Schema::create('order_invoices', function (Blueprint $table) {
            $table->id();
            $table->integer('tenant_id');
            $table->integer('branch_id');
            $table->string('status');
            $table->decimal('grand_total');
            $table->timestamps();
        });
        DB::table('branches')->insert([
            ['id' => 1, 'tenant_id' => 1, 'branch_name' => 'Main', 'city' => 'Kathmandu', 'latitude' => '0', 'longitude' => '0'],
            ['id' => 2, 'tenant_id' => 1, 'branch_name' => 'New', 'city' => null, 'latitude' => null, 'longitude' => null],
            ['id' => 3, 'tenant_id' => 2, 'branch_name' => 'Other tenant', 'city' => null, 'latitude' => null, 'longitude' => null],
        ]);
        foreach ([['paid', 100, '2026-09-01', 1], ['unpaid', 50, '2026-09-15', 1],
            ['cancelled', 900, '2026-09-10', 1], ['paid', 75, '2026-08-20', 1],
            ['paid', 999, '2026-09-10', 2]] as [$status, $total, $date, $tenant]) {
            DB::table('order_invoices')->insert([
                'tenant_id' => $tenant, 'branch_id' => 1, 'status' => $status,
                'grand_total' => $total, 'created_at' => $date . ' 00:00:00',
            ]);
        }
    }

    public function test_monthly_sales_are_scoped_and_include_branches_without_sales(): void
    {
        $rows = app(DashboardService::class)->buildBranchPerformance(1, 'Rs ');
        $this->assertCount(2, $rows);
        $this->assertSame(150.0, $rows[0]['revenue_value']);
        $this->assertSame('2', $rows[0]['orders_display']);
        $this->assertSame('+100.0% vs last month', $rows[0]['trend_label']);
        $this->assertStringContainsString('query=0%2C0', $rows[0]['map_url']);
        $this->assertSame(0.0, $rows[1]['revenue_value']);
        $this->assertSame('Location not added', $rows[1]['address']);
        $this->assertNull($rows[1]['map_url']);
    }

    public function test_filters_deleted_branches_and_address_fallback(): void
    {
        DB::table('branches')->where('id', 1)->update(['latitude' => '999', 'full_address' => 'Main Road']);
        $rows = app(DashboardService::class)->buildBranchPerformance(1, 'Rs ', 1);
        $this->assertCount(1, $rows);
        $this->assertStringContainsString('query=Main%20Road', $rows[0]['map_url']);
        $this->assertSame([], app(DashboardService::class)->buildBranchPerformance(1, 'Rs ', 3));
        session(['active_country_id' => 2]);
        $this->assertSame([], app(DashboardService::class)->buildBranchPerformance(1, 'Rs '));
        session()->forget('active_country_id');
        DB::table('branches')->where('id', 1)->update(['deleted_at' => now()]);
        $this->assertSame([], app(DashboardService::class)->buildBranchPerformance(1, 'Rs ', 1));
    }
}
