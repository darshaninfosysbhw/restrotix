<?php

namespace App\Http\Controllers\Admin\Table;

use App\Http\Controllers\Controller;
use App\Services\Admin\TableService; // Service ko import kiya
use App\Models\Table;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Http\Resources\Admin\TableResource;

class TableController extends Controller
{
    protected $tableService;

    // Constructor mein Service ko inject kar diya (Dependency Injection)
    public function __construct(TableService $tableService)
    {
        $this->tableService = $tableService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = Auth::user()->tenant_id;

        $branches = Branch::where('tenant_id', $tenantId)->get();

        // 🔥 CHANGE: optimized stats query + out_of_service added
        $stats = Table::where('tenant_id', $tenantId)
            ->selectRaw("
            COUNT(*) as total,
            SUM(status = 'available') as available,
            SUM(status = 'reserved') as reserved,
            SUM(status = 'occupied') as occupied,
            SUM(status = 'calling_waiter') as calling_waiter,
            SUM(status = 'out_of_service') as out_of_service
        ")
            ->first();


        $tableModels = Table::where('tenant_id', $tenantId)
            ->with(['orders' => function ($q) {
                // Sirf wo orders jo abhi tak finish nahi huye
                // $q->whereIn('status', ['pending', 'preparing', 'ready', 'served'])
                $q->where('status', 'running')
                    ->with('items');
            }])
            ->when($request->branch_id, function ($q) use ($request) {
                return $q->where('branch_id', $request->branch_id);
            })
            ->orderBy('table_number', 'asc')
            ->get();


        // 🔥 CHANGE: resource applied
        $data = [
            'tables' => collect(TableResource::collection($tableModels)->resolve()),
            'branches' => $branches,
            'stats' => $stats
        ];
        if ($user->role === 'waiter') {
            return view('modules.table.waiter.index', $data);
        }

        return view('modules.table.admin.index', $data);
    }


    public function bulkStore(Request $request)
    {
        $request->validate([
            'branch_id'    => 'required',
            'capacity'     => 'required|integer|min:1',
            'table_count'  => 'required|integer|min:1',
            'start_number' => 'required|integer|min:1',
        ]);

        $count = $this->tableService->generateBulkTables($request->all(), Auth::user()->tenant_id);

        return redirect()->back()->with('success', "$count tables generate ho gayi hain!");
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'branch_id'    => 'required',
            'table_number' => 'required|string',
            'capacity'     => 'required|integer|min:1',
            'status'       => 'required|in:available,occupied,reserved,calling_waiter,out_of_service',
        ]);

        try {
            // Service ko call kiya
            $this->tableService->updateTable($id, $request->all(), Auth::user()->tenant_id);

            return redirect()->back()->with('success', 'Table updated successfully!');
        } catch (\Exception $e) {
            // Agar Service ne exception throw kiya (jaise duplicate name)
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
