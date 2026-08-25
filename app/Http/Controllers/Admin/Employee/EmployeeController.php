<?php

namespace App\Http\Controllers\Admin\Employee;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\EmployeeResource;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    private function managedEmployeesQuery(User $user): Builder
    {
        return User::with(['detail', 'branch'])
            ->where('tenant_id', $user->tenant_id)
            ->whereIn('role', ['manager', 'chef', 'waiter', 'cashier'])
            ->when($user->role !== 'admin', function (Builder $query) use ($user) {
                $query->where('branch_id', $user->branch_id);
            });
    }

    private function findManagedEmployeeOrFail(User $user, int $employeeId): User
    {
        return $this->managedEmployeesQuery($user)
            ->whereKey($employeeId)
            ->firstOrFail();
    }

    private function validationRules(User $user, ?User $employee = null): array
    {
        $emailRule = Rule::unique('users', 'email');
        $phoneRule = Rule::unique('users', 'phone_number');

        if ($employee) {
            $emailRule = $emailRule->ignore($employee->id);
            $phoneRule = $phoneRule->ignore($employee->id);
        }

        $branchRules = $user->role === 'admin'
            ? [
                'required',
                Rule::exists('branches', 'id')->where(fn($query) => $query->where('tenant_id', $user->tenant_id)),
            ]
            : ['nullable'];

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', $emailRule],
            'phone_number' => ['required', 'string', 'max:20', $phoneRule],
            'pin_code' => ['required', 'digits:6'],
            'role' => ['required', Rule::in(['waiter', 'chef', 'cashier', 'manager'])],
            'branch_id' => $branchRules,
            'password' => $employee ? ['nullable', 'string', 'min:6'] : ['required', 'string', 'min:6'],
            'designation' => ['nullable', 'string', 'max:255'],
            'id_type' => ['nullable', 'string', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:255'],
            'emergency_contact_number' => ['nullable', 'string', 'max:20'],
            'current_address' => ['nullable', 'string', 'max:255'],
            'permanent_address' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'string', 'max:255'],
        ];
    }

    private function employeeDetailPayload(Request $request, bool $isCreate = false): array
    {
        $payload = [
            'designation' => $request->designation,
            'pin_code' => $request->pin_code,
            'id_type' => $request->id_type,
            'id_number' => $request->id_number,
            'emergency_contact_number' => $request->emergency_contact_number,
            'current_address' => $request->current_address,
            'permanent_address' => $request->permanent_address,
            'base_salary' => (float) ($request->input('base_salary') ?: 0),
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ];

        if ($isCreate) {
            $payload['employee_id'] = 'EMP-' . strtoupper(substr(uniqid(), -5));
            $payload['joining_date'] = now();
        }

        return $payload;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $search = trim((string) $request->input('search', ''));

        // --- Logic: Manager ko sirf apni branch ka data dikhe ---
        $query = User::with(['detail', 'branch'])
            ->where('tenant_id', $tenantId)
            ->whereIn('role', ['manager', 'chef', 'waiter', 'cashier']);

        if ($user->role !== 'admin') {
            $query->where('branch_id', $user->branch_id);
        }

        if ($search !== '') {
            $query->where(function (Builder $builder) use ($search) {
                $like = '%' . $search . '%';

                $builder->where('name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('phone_number', 'like', $like)
                    ->orWhere('role', 'like', $like)
                    ->orWhereHas('branch', function (Builder $branchQuery) use ($like) {
                        $branchQuery->where('branch_name', 'like', $like)
                            ->orWhere('city', 'like', $like);
                    })
                    ->orWhereHas('detail', function (Builder $detailQuery) use ($like) {
                        $detailQuery->where('designation', 'like', $like)
                            ->orWhere('employee_id', 'like', $like);
                    });
            });
        }

        $baseQuery = (clone $query)->latest();

        $employeesPaginator = (clone $baseQuery)
            ->paginate(25)
            ->withQueryString();

        $employeeModels = $employeesPaginator->getCollection();
        $employees = collect(EmployeeResource::collection($employeeModels)->resolve());

        $allEmployeeModels = (clone $baseQuery)->get();
        $allEmployees = collect(EmployeeResource::collection($allEmployeeModels)->resolve());

        $employeeStats = [
            'total' => $allEmployees->count(),
            'active' => $allEmployees->where('status', 'Active')->count(),
            'on_leave' => $allEmployees->where('status', 'Leave')->count(),
            'inactive' => $allEmployees->where('status', 'Inactive')->count(),
        ];

        // Form के लिए ब्रांचेस (Manager है तो सिर्फ उसकी अपनी ब्रांच ही भेजें)
        $branches = ($user->role === 'admin')
            ? Branch::where('tenant_id', $tenantId)->get()
            : Branch::where('id', $user->branch_id)->get();

        return view('admin.employee.index', compact('employees', 'employeesPaginator', 'branches', 'employeeStats'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), $this->validationRules($user));

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => $validator->errors()->first(), 'duration' => 5000],
            ]);
        }

        try {
            return DB::transaction(function () use ($request, $user) {
                $targetBranchId = $user->role === 'admin'
                    ? (int) $request->branch_id
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
                $newEmployee->detail()->create(
                    $this->employeeDetailPayload($request, true)
                );

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

    public function update(Request $request, User $employee)
    {
        $user = Auth::user();
        $employee = $this->findManagedEmployeeOrFail($user, $employee->id);

        $validator = Validator::make($request->all(), $this->validationRules($user, $employee));

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => $validator->errors()->first(), 'duration' => 5000],
            ]);
        }

        try {
            return DB::transaction(function () use ($request, $user, $employee) {
                $targetBranchId = $user->role === 'admin'
                    ? (int) $request->branch_id
                    : $user->branch_id;

                $payload = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'phone_number' => $request->phone_number,
                    'role' => $request->role,
                    'tenant_id' => $user->tenant_id,
                    'branch_id' => $targetBranchId,
                    'is_active' => $employee->is_active,
                ];

                if ($request->filled('password')) {
                    $payload['password'] = Hash::make($request->password);
                }

                $employee->update($payload);

                $detailPayload = $this->employeeDetailPayload($request);
                if ($employee->detail) {
                    $employee->detail()->update($detailPayload);
                } else {
                    $employee->detail()->create(
                        $this->employeeDetailPayload($request, true)
                    );
                }

                return redirect()->back()->with('toast', [
                    [
                        'type' => 'success',
                        'message' => 'Employee ' . $request->name . ' updated successfully!',
                        'duration' => 4000,
                    ],
                ]);
            });
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to update: ' . $e->getMessage(), 'duration' => 6000],
            ]);
        }
    }

    public function destroy(User $employee)
    {
        $user = Auth::user();
        $employee = $this->findManagedEmployeeOrFail($user, $employee->id);
        $employeeName = $employee->name;

        if ((int) $employee->id === (int) $user->id) {
            return redirect()->back()->with('toast', [
                [
                    'type' => 'error',
                    'message' => 'You cannot delete your own account from the employee list.',
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            DB::transaction(function () use ($employee) {
                $employee->delete();
            });

            return redirect()->back()->with('toast', [
                [
                    'type' => 'success',
                    'message' => 'Employee ' . $employeeName . ' removed successfully!',
                    'duration' => 4000,
                ],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to delete: ' . $e->getMessage(), 'duration' => 6000],
            ]);
        }
    }
}
