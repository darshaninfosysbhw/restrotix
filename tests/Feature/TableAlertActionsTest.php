<?php

namespace Tests\Feature;

use App\Http\Controllers\Modules\Table\TableController;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class TableAlertActionsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'alerts_test', 'database.connections.alerts_test' => [
            'driver' => 'sqlite', 'database' => ':memory:', 'prefix' => '',
        ]]);
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->integer('tenant_id');
            $table->integer('branch_id');
            $table->string('status');
            $table->boolean('is_calling_waiter');
            $table->boolean('is_bill_requested');
            $table->timestamps();
        });
        Schema::create('table_service_requests', function (Blueprint $table) {
            $table->id();
            $table->integer('table_id');
            $table->string('type');
            $table->string('status');
            $table->integer('handled_by_waiter_id')->nullable();
            $table->timestamp('requested_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
        DB::table('tables')->insert(['id' => 1, 'tenant_id' => 1, 'branch_id' => 2,
            'status' => 'occupied', 'is_calling_waiter' => true, 'is_bill_requested' => true]);
        foreach (['call_waiter', 'bill_request'] as $type) {
            DB::table('table_service_requests')->insert(['table_id' => 1, 'type' => $type, 'status' => 'pending', 'requested_at' => now()]);
        }
        $user = new User(['role' => 'admin', 'tenant_id' => 1, 'branch_id' => 1]);
        $user->id = 10;
        Auth::setUser($user);
        session(['active_branch_id' => 2]);
    }

    public function test_admin_can_clear_selected_branch_alerts_independently_and_repeat_safely(): void
    {
        $controller = app(TableController::class);
        $controller->acceptWaiterCall(Table::findOrFail(1));
        $controller->acceptWaiterCall(Table::findOrFail(1));
        $table = Table::findOrFail(1);
        $this->assertFalse($table->is_calling_waiter);
        $this->assertTrue($table->is_bill_requested);
        $this->assertSame('pending', DB::table('table_service_requests')->where('type', 'bill_request')->value('status'));
        $controller->clearBillRequest($table);
        $this->assertFalse($table->fresh()->is_bill_requested);
        $this->assertSame('occupied', $table->fresh()->status);
        $this->assertSame(2, DB::table('table_service_requests')->where('status', 'completed')->count());
    }

    public function test_manager_cannot_clear_other_branch_even_if_session_is_changed(): void
    {
        Auth::user()->role = 'manager';
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(TableController::class)->acceptWaiterCall(Table::findOrFail(1));
    }

    public function test_admin_cannot_clear_other_tenants_alert(): void
    {
        Auth::user()->tenant_id = 3;
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        app(TableController::class)->clearBillRequest(Table::findOrFail(1));
    }

    public function test_assigned_manager_can_clear_bill_request(): void
    {
        Auth::user()->role = 'manager';
        Auth::user()->branch_id = 2;
        $result = app(TableController::class)->clearBillRequest(Table::findOrFail(1))->getData(true);
        $this->assertFalse($result['is_bill_requested']);
        $this->assertTrue($result['is_calling_waiter']);
    }
}
