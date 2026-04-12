<?php

namespace App\Http\Controllers\Admin\Branch;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BranchResource;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $tenant = Auth::user()->tenant;

        // सिर्फ current tenant की branches + related manager + users count
        $branchModels = $tenant->branches()
            ->withCount('users')
            ->with(['manager:id,branch_id,name,email,is_active,role'])
            ->latest()
            ->get();

        // Resource mapping (same pattern as superadmin module)
        $branches = collect(BranchResource::collection($branchModels)->resolve());

        // Dynamic status counts from mapped payload
        $stats = [
            'total' => $branches->count(),
            'active' => $branches->where('status', 'Active')->count(),
            'setup' => $branches->where('status', 'Setup')->count(),
            'inactive' => $branches->where('status', 'Inactive')->count(),
        ];

        return view('admin.branches.index', compact('branches', 'stats'));
    }

    public function store(Request $request)
    {

        $user = Auth::user();
        $tenant = $user->tenant;
        $plan = $tenant->plan;

        // 1. Plan Limit Check (The Gatekeeper)
        $currentBranchCount = $tenant->branches()->count();
        if ($currentBranchCount >= ($plan->max_branches ?? 1)) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => 'Limit reached! Please upgrade your plan to add more branches.',
                    'duration' => 5000,
                ],
            ]);
        }

        // 2. Validation
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
            'contact_number' => 'required',
            'branch_email' => 'nullable|email|max:255',
            'city' => 'required',
            'country_code' => 'required|in:Ind,Nep,UAE',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ],
            ]);
        }

        try {
            // 3. Country-wise Auto Mapping (No manual typing for user)
            $countryMap = [
                'Ind' => ['currency' => 'INR', 'timezone' => 'Asia/Kolkata'],
                'Nep' => ['currency' => 'NPR', 'timezone' => 'Asia/Kathmandu'],
                'UAE' => ['currency' => 'AED', 'timezone' => 'Asia/Dubai'],
            ];

            $mapping = $countryMap[$request->country_code];

            // 4. Create Branch
            $branch = Branch::create([
                'tenant_id' => $tenant->id,
                'branch_name' => $request->branch_name,
                'contact_number' => $request->contact_number,
                'branch_email' => $request->branch_email,
                'country_code' => $request->country_code,
                'city' => $request->city,
                'state' => $request->state,
                'pincode' => $request->pincode,
                'full_address' => $request->full_address,
                'currency' => $mapping['currency'],
                'timezone' => $mapping['timezone'],
                'offline_billing_enabled' => $request->has('offline_billing_enabled') ? 1 : 0,
            ]);

            // 5. Manager Assignment (अगर सेलेक्ट किया है)
            if ($request->manager_id) {
                $manager = User::find($request->manager_id);
                if ($manager) {
                    $manager->update(['branch_id' => $branch->id]);
                }
            }

            return redirect()->route('admin.branches.index')->with('toast', [
                [
                    'type' => 'success',
                    'message' => 'Branch created successfully!',
                    'duration' => 4000,
                ],
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => 'Unable to create branch: ' . $e->getMessage(),
                    'duration' => 6000,
                ],
            ]);
        }
    }
}
