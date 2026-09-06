<?php

namespace App\Http\Controllers\Modules\MenuManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MenuManagement\CategoryResource;
use App\Models\Branch;
use App\Models\MenuCategory;
use App\Models\User;
use App\Services\Admin\MenuManagement\CategoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    protected $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    private function managerBranchId(User $user): ?int
    {
        if (strtolower((string) $user->role) !== 'manager') {
            return null;
        }

        $candidateBranchId = $user->branch_id ?: session('active_branch_id');
        $branchId = Branch::query()
            ->where('tenant_id', $user->tenant_id)
            ->when($candidateBranchId, fn ($query) => $query->whereKey($candidateBranchId))
            ->value('id');

        if (!$branchId) {
            $branchId = Branch::query()
                ->where('tenant_id', $user->tenant_id)
                ->orderBy('branch_name')
                ->value('id');
        }

        return $branchId ? (int) $branchId : null;
    }

    /**
     * Display listing of categories
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $this->managerBranchId($user);
        $search = trim((string) $request->input('search', ''));

        $categoryPaginator = $this->categoryService->getPaginatedCategories($tenantId, 25, $search, $branchId);
        $categories = CategoryResource::collection($categoryPaginator->getCollection())->resolve();
        $parentCategories = $this->categoryService->getParentCategories($tenantId, $branchId);
        $branches = $this->categoryService->getTenantBranches($tenantId, $branchId);
        $stats = $this->categoryService->getCategoryStats($tenantId, $branchId);

        return view('modules.menu-management.categories', compact('categories', 'categoryPaginator', 'parentCategories', 'branches', 'stats', 'branchId'));
    }

    /**
     * Store a new category (Main or Sub)
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $this->managerBranchId($user);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_categories', 'id')->where(fn($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->when($branchId !== null, fn($query) => $query->where(function ($query) use ($branchId) {
                        $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
                    }))),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->when($branchId !== null, fn($query) => $query->where('id', $branchId))),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('menu_categories', 'code')->where(fn($query) => $query->where('tenant_id', $tenantId)),
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $validator->after(function ($validator) use ($request, $tenantId, $branchId) {
            $selectedBranchId = $branchId ?? ($request->filled('branch_id') ? (int) $request->input('branch_id') : null);

            $existsQuery = MenuCategory::query()
                ->where('tenant_id', $tenantId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $request->input('name'))]);

            if ($selectedBranchId === null) {
                $existsQuery->whereNull('branch_id');
            } else {
                $existsQuery->where('branch_id', $selectedBranchId);
            }

            if ($existsQuery->exists()) {
                $validator->errors()->add('name', 'This category name already exists for the selected branch.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => $validator->errors()->first(),
                'duration' => 5000,
            ]]);
        }

        $validated = $validator->validated();
        $validated['tenant_id'] = $tenantId;
        $validated['parent_id'] = $validated['parent_id'] ?? null;
        $validated['branch_id'] = $branchId ?? ($validated['branch_id'] ?? null);
        $validated['is_active'] = (bool) $validated['is_active'];

        try {
            $this->categoryService->storeCategory($validated);

            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'success',
                'message' => 'Category created successfully!',
                'duration' => 3500,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->back()->withInput()->with('toast', [[
                'type' => 'error',
                'message' => 'Category create failed: ' . $exception->getMessage(),
                'duration' => 6000,
            ]]);
        }
    }

    public function update(Request $request, int $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $this->managerBranchId($user);

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn($query) => $query->where('branch_id', $branchId))
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_categories', 'id')->where(fn($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->when($branchId !== null, fn($query) => $query->where(function ($query) use ($branchId) {
                        $query->whereNull('branch_id')->orWhere('branch_id', $branchId);
                    }))),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn($query) => $query
                    ->where('tenant_id', $tenantId)
                    ->when($branchId !== null, fn($query) => $query->where('id', $branchId))),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('menu_categories', 'code')
                    ->ignore($category->id)
                    ->where(fn($query) => $query->where('tenant_id', $tenantId)),
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'required|boolean',
        ]);

        $validator->after(function ($validator) use ($request, $tenantId, $category, $branchId) {
            if ((int) $request->input('parent_id') === (int) $category->id) {
                $validator->errors()->add('parent_id', 'Category cannot be its own parent.');
                return;
            }

            $selectedBranchId = $branchId ?? ($request->filled('branch_id') ? (int) $request->input('branch_id') : null);

            $existsQuery = MenuCategory::query()
                ->where('tenant_id', $tenantId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $request->input('name'))])
                ->where('id', '!=', $category->id);

            if ($selectedBranchId === null) {
                $existsQuery->whereNull('branch_id');
            } else {
                $existsQuery->where('branch_id', $selectedBranchId);
            }

            if ($existsQuery->exists()) {
                $validator->errors()->add('name', 'This category name already exists for the selected branch.');
            }
        });

        if ($validator->fails()) {
            return redirect()->back()->withInput()->with([
                'edit_category_id' => $category->id,
                'toast' => [[
                    'type' => 'error',
                    'message' => $validator->errors()->first(),
                    'duration' => 5000,
                ]],
            ]);
        }

        $validated = $validator->validated();
        $validated['parent_id'] = $validated['parent_id'] ?? null;
        $validated['branch_id'] = $branchId ?? ($validated['branch_id'] ?? null);
        $validated['is_active'] = (bool) $validated['is_active'];

        try {
            $this->categoryService->updateCategory($category, $validated);

            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'success',
                'message' => 'Category updated successfully!',
                'duration' => 3500,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->back()->withInput()->with([
                'edit_category_id' => $category->id,
                'toast' => [[
                    'type' => 'error',
                    'message' => 'Category update failed: ' . $exception->getMessage(),
                    'duration' => 6000,
                ]],
            ]);
        }
    }

    public function toggleStatus(Request $request, int $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $this->managerBranchId($user);

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn($query) => $query->where('branch_id', $branchId))
            ->findOrFail($id);

        $validated = $request->validate([
            'is_active' => 'required|boolean',
        ]);

        try {
            $this->categoryService->setCategoryStatus($category, (bool) $validated['is_active']);

            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'success',
                'message' => 'Category status updated successfully!',
                'duration' => 2500,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'error',
                'message' => 'Status update failed: ' . $exception->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }

    public function destroy(int $id)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;
        $branchId = $this->managerBranchId($user);

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->when($branchId !== null, fn($query) => $query->where('branch_id', $branchId))
            ->findOrFail($id);

        try {
            $this->categoryService->deleteCategory($category);

            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'success',
                'message' => 'Category deleted successfully!',
                'duration' => 3000,
            ]]);
        } catch (\Throwable $exception) {
            return redirect()->route('admin.menu.categories.index')->with('toast', [[
                'type' => 'error',
                'message' => 'Delete failed: ' . $exception->getMessage(),
                'duration' => 5000,
            ]]);
        }
    }
}
