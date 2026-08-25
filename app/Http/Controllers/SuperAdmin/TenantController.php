<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SuperAdmin\TenantResource;
use App\Models\Tenant;
use App\Models\User;
use App\Models\Branch;
use App\Models\Plan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TenantController extends Controller
{
    public function index(Request $request)
    {
        // Table ke liye paginated tenants
        $tenantsQuery = Tenant::with([
            'users:id,tenant_id,email,phone_number,role',
            'branches:id,tenant_id,city',
            'plan:id,name,slug',
        ])
            ->withCount('branches')
            ->latest();

        $status = $request->query('status');
        if ($status === 'active') {
            $tenantsQuery->active();
        } elseif ($status === 'trial') {
            $tenantsQuery->trial();
        } elseif ($status === 'pending') {
            $tenantsQuery->pending();
        }

        $expiringDays = (int) $request->query('expiring_days', 0);
        if ($expiringDays > 0) {
            $tenantsQuery->trial()->whereDate('subscription_ends_at', '<=', now()->addDays($expiringDays)->toDateString());
        }

        $tenants = $tenantsQuery->paginate(10)->withQueryString();

        $restaurants = collect(TenantResource::collection($tenants->getCollection())->resolve());
        $tenants->setCollection($restaurants);

        $restaurantsCollection = $restaurants;
        $restaurantStats = [
            'total' => Tenant::count(),
            'active' => Tenant::where('is_banned', false)
                ->where(function ($query) {
                    $query->whereNull('subscription_ends_at')
                        ->orWhere('subscription_ends_at', '<=', now());
                })
                ->count(),
            'trial' => Tenant::where('is_banned', false)
                ->whereNotNull('subscription_ends_at')
                ->where('subscription_ends_at', '>', now())
                ->count(),
            'branches' => Branch::count(),
        ];

        // Database se plans fetch karein
        $currencyId = session('currency_id');

        $plans = Plan::with(['prices' => function ($query) use ($currencyId) {
            $query->where('currency_id', $currencyId);
        }])->where('is_active', true)->get();

        return view('superadmin.tenants.index', [
            'restaurants' => $tenants,
            'restaurantStats' => $restaurantStats,
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        // 1. Validation (toast-friendly)
        $validator = Validator::make($request->all(), [
            'company_name'              => 'required|string|max:255',
            'slug'                      => 'required|string|unique:tenants,slug|max:50|regex:/^[a-z0-9-]+$/', // Only lowercase, numbers, and hyphens
            'owner_name'                => 'required|string|max:255',
            'email'                     => 'required|email|unique:users,email',
            'phone'                     => 'required|string|max:20|unique:users,phone_number',
            'city'                      => 'required|string|max:255',
            'country_id'                => 'required|exists:countries,id',
            'subscription_plan'         => 'required|exists:plans,id',
            'billing_cycle'             => 'required|in:monthly,yearly',
            'subscription_status'       => 'nullable|in:trial,active,expired,canceled',
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
            $billingCycle = strtolower((string) $request->billing_cycle);
            if (!in_array($billingCycle, ['monthly', 'yearly'], true)) {
                $billingCycle = 'monthly';
            }

            $subscriptionStatus = strtolower((string) ($request->subscription_status ?? 'trial'));

            // 2. Database Transaction (ताकि अगर एक भी स्टेप फेल हो तो कुछ भी सेव न हो)
            DB::beginTransaction();

            // STEP 1: Create Tenant
            $tenant = Tenant::create([
                'company_name'               => $request->company_name,
                'slug'                       => $request->slug,
                'owner_name'                 => $request->owner_name,
                'country_id'                 => $request->country_id,
                'subscription_status'        => $subscriptionStatus,
                'subscription_ends_at'       => in_array($subscriptionStatus, ['trial'], true) ? now()->addDays(14) : null,
                'is_banned'                  => in_array($subscriptionStatus, ['canceled'], true),
                'plan_id'                    => $request->subscription_plan,
                'billing_cycle'              => $billingCycle,
            ]);

            // STEP 2: Create Default Branch
            $branch = Branch::create([
                'tenant_id'                   => $tenant->id,
                'branch_name'                 => 'Main Outlet',
                'country_id'                  => $request->country_id,
                'city'                        => $request->city,
                'contact_number'              => $request->phone,
            ]);

            // STEP 3: Create Admin User for this Tenant
            $user = User::create([
                'name'                        => $request->owner_name,
                'email'                       => $request->email,
                'phone_number'                => $request->phone,
                'password'                    => Hash::make('password123'), // Default Password
                'role'                        => 'admin', // या restaurant_admin जो आपकी माइग्रेशन में है
                'tenant_id'                   => $tenant->id,
                'branch_id'                   => $branch->id,
                'is_active'                   => true,
            ]);

            DB::commit();

            // return redirect()->back()->with('success', 'Restaurant Onboarded Successfully! Default password is: password123');
            // ✅ Toast Manager format
            return redirect()->back()->with('toast', [
                ['type' => 'success', 'message' => 'Restaurant Onboarded Successfully! Default password is: password123', 'duration' => 10000]
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            // return redirect()->back()->with('error', 'Something went wrong: ' . $e->getMessage());
            // ❌ Error toast
            return redirect()->back()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    public function update(Request $request, Tenant $tenant)
    {
        $adminUser = User::where('tenant_id', $tenant->id)
            ->where('role', 'admin')
            ->first() ?? User::where('tenant_id', $tenant->id)->orderBy('id')->first();

        $branch = Branch::where('tenant_id', $tenant->id)->orderBy('id')->first();

        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'slug' => [
                'required',
                'string',
                'max:50',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('tenants', 'slug')->ignore($tenant->id),
            ],
            'owner_name'   => 'required|string|max:255',
            'email'        => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore(optional($adminUser)->id),
            ],
            'phone'        => [
                'required',
                'string',
                'max:20',
                Rule::unique('users', 'phone_number')->ignore(optional($adminUser)->id),
            ],
            'city'         => 'required|string|max:255',
            'subscription_plan' => 'required|exists:plans,id',
            'country_id' => 'required|exists:countries,id',
            'billing_cycle' => 'required|in:monthly,yearly',
            'subscription_status' => 'required|in:trial,active,expired,canceled',
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
            DB::beginTransaction();

            $billingCycle = strtolower((string) $request->billing_cycle);
            if (!in_array($billingCycle, ['monthly', 'yearly'], true)) {
                $billingCycle = 'monthly';
            }

            $subscriptionStatus = strtolower((string) $request->subscription_status);

            $tenantPayload = [
                'company_name'           => $request->company_name,
                'slug'                   => $request->slug,
                'owner_name'             => $request->owner_name,
                'plan_id'                => $request->subscription_plan,
                'country_id'             => $request->country_id,
                'billing_cycle'          => $billingCycle,
                'subscription_status'    => $subscriptionStatus,
            ];

            if ($subscriptionStatus === 'canceled') {
                $tenantPayload['is_banned'] = true;
                $tenantPayload['subscription_ends_at'] = null;
            } elseif ($subscriptionStatus === 'trial') {
                $tenantPayload['is_banned'] = false;
                $tenantPayload['subscription_ends_at'] = now()->addDays(14);
            } else {
                $tenantPayload['is_banned'] = false;
                $tenantPayload['subscription_ends_at'] = null;
            }

            $tenant->update($tenantPayload);

            Branch::withoutGlobalScopes()->where('tenant_id', $tenant->id)->update([
                'country_id' => $request->country_id,
            ]);

            if ($branch) {
                $branch->update([
                    'city'            => $request->city,
                    'contact_number'  => $request->phone,
                    'country_id'      => $request->country_id,
                ]);
            }

            if ($adminUser) {
                $adminUser->update([
                    'name'              => $request->owner_name,
                    'email'             => $request->email,
                    'phone_number'      => $request->phone,
                ]);
            }

            DB::commit();

            return redirect()->route('superadmin.tenants.index')->with('toast', [
                ['type' => 'success', 'message' => 'Restaurant updated successfully.', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withInput()->with('toast', [
                ['type' => 'error', 'message' => 'Something went wrong: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }

    public function destroy(Tenant $tenant)
    {
        try {
            DB::beginTransaction();

            User::where('tenant_id', $tenant->id)->delete();
            $tenant->delete();

            DB::commit();

            return redirect()->route('superadmin.tenants.index')->with('toast', [
                ['type' => 'success', 'message' => 'Restaurant deleted successfully.', 'duration' => 5000]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->with('toast', [
                ['type' => 'error', 'message' => 'Unable to delete restaurant: ' . $e->getMessage(), 'duration' => 5000]
            ]);
        }
    }
}
