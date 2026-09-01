<?php

namespace App\Http\Controllers\Admin\Branch;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\BranchResource;
use App\Models\Branch;
use App\Models\Country;
use App\Models\Currency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index(Request $request)
    {
        $tenant = Auth::user()->tenant;
        $search = trim((string) $request->input('search', ''));

        $baseQuery = $tenant->branches()
            ->withCount('users')
            ->with([
                'manager:id,branch_id,name,email,is_active,role',
                'country:id,name,iso_code',
                'currency:id,code,symbol',
            ])
            ->latest();

        if ($search !== '') {
            $baseQuery->where(function ($query) use ($search) {
                $like = '%' . $search . '%';

                $query->where('branch_name', 'like', $like)
                    ->orWhere('branch_email', 'like', $like)
                    ->orWhere('contact_number', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhere('state', 'like', $like)
                    ->orWhere('pincode', 'like', $like)
                    ->orWhere('full_address', 'like', $like)
                    ->orWhereHas('manager', function ($managerQuery) use ($like) {
                        $managerQuery->where('name', 'like', $like)
                            ->orWhere('email', 'like', $like);
                    })
                    ->orWhereHas('country', function ($countryQuery) use ($like) {
                        $countryQuery->where('name', 'like', $like)
                            ->orWhere('iso_code', 'like', $like);
                    });
            });
        }

        $branchesPaginator = (clone $baseQuery)
            ->paginate(25)
            ->withQueryString();

        $branchModels = $branchesPaginator->getCollection();
        $branches = collect(BranchResource::collection($branchModels)->resolve());

        $allBranchModels = (clone $baseQuery)->get();
        $allBranches = collect(BranchResource::collection($allBranchModels)->resolve());

        // Dynamic status counts from mapped payload
        $stats = [
            'total' => $allBranches->count(),
            'active' => $allBranches->where('status', 'Active')->count(),
            'setup' => $allBranches->where('status', 'Setup')->count(),
            'inactive' => $allBranches->where('status', 'Inactive')->count(),
        ];

        return view('admin.branches.index', compact('branches', 'branchesPaginator', 'stats'));
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
        $validator = Validator::make($request->all(), $this->branchRules((int) $tenant->id));
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
            // 3. Create Branch
            $branch = Branch::unguarded(function () use ($tenant, $request) {
                return Branch::create($this->branchPayload($tenant->id, $request));
            });

            // 4. Manager Assignment (अगर सेलेक्ट किया है)
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

    public function update(Request $request, Branch $branch)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        abort_unless($user && $user->tenant_id && (int) $branch->tenant_id === (int) $tenant->id, 403);

        $validator = Validator::make($request->all(), $this->branchRules((int) $tenant->id, $branch));
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
            Branch::unguarded(function () use ($branch, $tenant, $request) {
                $branch->fill($this->branchPayload($tenant->id, $request));
                $branch->save();
            });

            return redirect()->route('admin.branches.index')->with('toast', [
                [
                    'type' => 'success',
                    'message' => 'Branch updated successfully!',
                    'duration' => 4000,
                ],
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->withInput()->with('toast', [
                [
                    'type' => 'error',
                    'message' => 'Unable to update branch: ' . $e->getMessage(),
                    'duration' => 6000,
                ],
            ]);
        }
    }

    public function destroy(Request $request, Branch $branch)
    {
        $user = Auth::user();
        $tenant = $user->tenant;
        abort_unless($user && $user->tenant_id && (int) $branch->tenant_id === (int) $tenant->id, 403);

        try {
            $branch->delete();

            return redirect()->route('admin.branches.index')->with('toast', [
                [
                    'type' => 'success',
                    'message' => 'Branch deleted successfully!',
                    'duration' => 4000,
                ],
            ]);
        } catch (\Throwable $e) {
            return redirect()->back()->with('toast', [
                [
                    'type' => 'error',
                    'message' => 'Unable to delete branch: ' . $e->getMessage(),
                    'duration' => 6000,
                ],
            ]);
        }
    }

    private function branchRules(int $tenantId, ?Branch $branch = null): array
    {
        $uniqueBranchName = Rule::unique('branches', 'branch_name')
            ->where(fn($query) => $query->where('tenant_id', $tenantId));

        if ($branch) {
            $uniqueBranchName->ignore($branch->id);
        }

        return [
            'branch_name' => ['required', 'string', 'max:255', $uniqueBranchName],
            'contact_number' => ['required', 'string', 'max:20'],
            'branch_email' => ['nullable', 'email', 'max:255'],
            'country_code' => ['required', Rule::in(['Ind', 'Nep', 'UAE'])],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['nullable', 'string', 'max:255'],
            'pincode' => ['nullable', 'string', 'max:10'],
            'full_address' => ['nullable', 'string'],
            'tax_setting' => ['nullable', Rule::in(['exclusive', 'inclusive'])],
            'tax_rate' => ['nullable', 'numeric', 'min:0'],
            'offline_billing_enabled' => ['nullable', 'boolean'],
        ];
    }

    private function branchPayload(int $tenantId, Request $request): array
    {
        $context = $this->resolveCountryContext((string) $request->input('country_code', 'Ind'));

        $payload = [
            'tenant_id' => $tenantId,
            'branch_name' => trim((string) $request->input('branch_name', '')),
            'contact_number' => trim((string) $request->input('contact_number', '')),
            'branch_email' => $request->filled('branch_email') ? trim((string) $request->input('branch_email')) : null,
            'city' => trim((string) $request->input('city', '')),
            'state' => $request->filled('state') ? trim((string) $request->input('state')) : null,
            'pincode' => $request->filled('pincode') ? trim((string) $request->input('pincode')) : null,
            'full_address' => $request->filled('full_address') ? trim((string) $request->input('full_address')) : null,
            'offline_billing_enabled' => $request->boolean('offline_billing_enabled'),
            'tax_setting' => $request->input('tax_setting', 'exclusive'),
            'tax_rate' => $request->filled('tax_rate') ? $request->input('tax_rate') : 5.0,
        ];

        if (Schema::hasColumn('branches', 'country_id')) {
            $payload['country_id'] = $context['country_id'];
        }

        if (Schema::hasColumn('branches', 'currency_id')) {
            $payload['currency_id'] = $context['currency_id'];
        }

        if (Schema::hasColumn('branches', 'timezone')) {
            $payload['timezone'] = $context['timezone'];
        }

        if (Schema::hasColumn('branches', 'country_code')) {
            $payload['country_code'] = $context['legacy_country_code'];
        }

        if (Schema::hasColumn('branches', 'currency')) {
            $payload['currency'] = $context['legacy_currency_code'];
        }

        return $payload;
    }

    private function resolveCountryContext(string $countryCode): array
    {
        $definition = match ($countryCode) {
            'Nep' => [
                'legacy_country_code' => 'Nep',
                'country_iso' => 'NP',
                'country_names' => ['Nepal'],
                'legacy_currency_code' => 'NPR',
                'currency_code' => 'NPR',
                'currency_names' => ['Nepalese Rupee'],
                'timezone' => 'Asia/Kathmandu',
            ],
            'UAE' => [
                'legacy_country_code' => 'UAE',
                'country_iso' => 'AE',
                'country_names' => ['United Arab Emirates', 'UAE'],
                'legacy_currency_code' => 'AED',
                'currency_code' => 'AED',
                'currency_names' => ['United Arab Emirates Dirham', 'UAE Dirham', 'Dirham'],
                'timezone' => 'Asia/Dubai',
            ],
            default => [
                'legacy_country_code' => 'Ind',
                'country_iso' => 'IN',
                'country_names' => ['India'],
                'legacy_currency_code' => 'INR',
                'currency_code' => 'INR',
                'currency_names' => ['Indian Rupee'],
                'timezone' => 'Asia/Kolkata',
            ],
        };

        $country = Country::query()
            ->where('iso_code', $definition['country_iso'])
            ->orWhereIn('name', $definition['country_names'])
            ->first();

        $currency = Currency::query()
            ->where('code', $definition['currency_code'])
            ->orWhereIn('name', $definition['currency_names'])
            ->first();

        return [
            'country_id' => $country?->id,
            'currency_id' => $currency?->id,
            'timezone' => $definition['timezone'],
            'legacy_country_code' => $definition['legacy_country_code'],
            'legacy_currency_code' => $definition['legacy_currency_code'],
        ];
    }
}
