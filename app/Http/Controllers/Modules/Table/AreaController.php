<?php

namespace App\Http\Controllers\Modules\Table;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AreaController extends Controller
{
    public function index(Request $request)
   {
    $user = auth()->user();

    $tenantId = session('active_tenant_id', $user->tenant_id);
    $branchId = session('active_branch_id', $user->branch_id);

    $search = trim((string) $request->query('search', ''));

    /*
    |--------------------------------------------------------------------------
    | Areas List
    |--------------------------------------------------------------------------
    */
    $areas = Area::query()
        ->where('tenant_id', $tenantId)
        ->where('branch_id', $branchId)
        ->withCount('tables')
        ->when($search !== '', function ($query) use ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        })
        ->latest()
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Stats
    | Search se stats change nahi honge
    |--------------------------------------------------------------------------
    */
    $baseQuery = Area::query()
        ->where('tenant_id', $tenantId)
        ->where('branch_id', $branchId);

    $stats = [
        'total' => (clone $baseQuery)->count(),

        'active' => (clone $baseQuery)
            ->where('is_active', true)
            ->count(),

        'setup' => (clone $baseQuery)
            ->doesntHave('tables')
            ->count(),

        'inactive' => (clone $baseQuery)
            ->where('is_active', false)
            ->count(),
    ];

    return view('modules.table.admin.areas.index', compact(
        'areas',
        'stats'
    ));
   }

    public function store(Request $request)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('areas', 'code')->where(
                    fn ($query) => $query
                        ->where('tenant_id', $tenantId)
                        ->where('branch_id', $branchId)
                ),
            ],
        ], [
            'code.unique' => 'This area / floor code is already used. Please try another code.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $validator->errors()->first(),
                    'errors' => $validator->errors(),
                ], 422);
            }

            return back()->withErrors($validator)->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        $area = Area::create([
            'tenant_id' => $tenantId,
            'branch_id' => $branchId,

            'name' => $request->name,
            'code' => strtoupper(trim($request->code)),

            'is_active' => $request->has('is_active'),
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Area / floor created successfully.',
                'area' => [
                    'id' => $area->id,
                    'name' => $area->name,
                    'code' => $area->code,
                ],
            ], 201);
        }

        return back()->with('toast', [[
            'type' => 'success',
            'message' => 'Area / floor created successfully.',
            'duration' => 4000,
        ]]);
    }

    public function update(Request $request, Area $area)
    {
        $tenantId = session('active_tenant_id', auth()->user()->tenant_id);
        $branchId = session('active_branch_id', auth()->user()->branch_id);

        $request->merge([
            'code' => strtoupper(trim((string) $request->input('code'))),
        ]);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('areas', 'code')
                    ->where(fn ($query) => $query
                        ->where('tenant_id', $tenantId)
                        ->where('branch_id', $branchId))
                    ->ignore($area->id),
            ],
        ], [
            'code.unique' => 'This area / floor code is already used. Please try another code.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        $area->update([
            'name' => $request->name,
            'code' => strtoupper(trim($request->code)),
            'is_active' => $request->has('is_active'),
        ]);

        return back()->with('toast', [[
            'type' => 'success',
            'message' => 'Area / floor updated successfully.',
            'duration' => 4000,
        ]]);
    }

    public function destroy(Area $area)
    {
        if ($area->tables()->exists()) {
            return back()->with(
                'error',
                'Cannot delete area! Tables are currently assigned to this area.'
            );
        }

        $area->delete();

        return back()->with(
            'success',
            'Area deleted successfully!'
        );
    }

    public function toggleStatus(Area $area)
    {
        $area->update([
            'is_active' => ! $area->is_active,
        ]);

        return back()->with(
            'success',
            'Status updated successfully!'
        );
    }
}
