<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmployeeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        // --- Logic: Manager ko sirf apni branch ka data dikhe ---
        $query = User::with(['detail', 'branch'])
            ->where('tenant_id', $tenantId)
            ->whereIn('role', ['manager', 'chef', 'waiter', 'cashier']);

        if ($user->role !== 'admin') {
            $query->where('branch_id', $user->branch_id);
        }

        $employeeModels = $query->latest()->get();
        // -------------------------------------------------------

        $employees = collect(EmployeeResource::collection($employeeModels)->resolve());

        $employeeStats = [
            'total' => $employees->count(),
            'active' => $employees->where('status', 'Active')->count(),
            'on_leave' => $employees->where('status', 'Leave')->count(),
            'inactive' => $employees->where('status', 'Inactive')->count(),
        ];

        // Form के लिए ब्रांचेस (Manager है तो सिर्फ उसकी अपनी ब्रांच ही भेजें)
        $branches = ($user->role === 'admin')
            ? Branch::where('tenant_id', $tenantId)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('admin.employee.index', compact('employees', 'branches', 'employeeStats'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'pin_code' => 'required|digits:6',
            'role' => 'required',
            // Manager agar bypass karne ki koshish kare to bhi admin logic protect karega
            'branch_id' => $user->role === 'admin' ? 'required' : 'nullable',
            'password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => $validator->errors()->first(), 'duration' => 5000],
            ]);
        }

        try {
            return DB::transaction(function () use ($request, $user) {

                // --- Logic: Branch Fix ---
                $targetBranchId = ($user->role === 'admin')
                    ? $request->branch_id
                    : $user->branch_id;

                // A. Create User
                $newEmployee = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'password' => Hash::make($request->password),
                    'role' => $request->role,
                    'tenant_id' => $user->tenant_id,
                    'branch_id' => $targetBranchId, // Fixed Branch
                    'is_active' => $request->is_active ?? 1,
                ]);

                // B. Create Employee Detail
                $newEmployee->detail()->create([
                    'employee_id' => 'EMP-' . strtoupper(substr(uniqid(), -5)),
                    'designation' => $request->designation,
                    'pin_code' => $request->pin_code,
                    'id_type' => $request->id_type,
                    'id_number' => $request->id_number,
                    'emergency_contact_number' => $request->emergency_contact_number,
                    'current_address' => $request->current_address,
                    'permanent_address' => $request->permanent_address,
                    'base_salary' => $request->base_salary ?? 0,
                    'bank_name' => $request->bank_name,
                    'account_number' => $request->account_number,
                    'shift' => $request->shift,
                    'joining_date' => now(),
                ]);

                return redirect()->back()->with('toast', [
                    [
                        'type' => 'success',
                        'message' => 'Employee ' . $request->name . ' registered successfully!',
                        'duration' => 4000
                    ],
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to register: ' . $e->getMessage(), 'duration' => 6000],
            ]);
        }
    }
}
