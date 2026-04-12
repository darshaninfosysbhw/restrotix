<?php

namespace App\Http\Controllers\Admin\MenuManagement;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\MenuManagement\CategoryResource;
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

    /**
     * Display listing of categories
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $categoryModels = $this->categoryService->getAllCategories($tenantId);
        $categories = CategoryResource::collection($categoryModels)->resolve();
        $parentCategories = $this->categoryService->getParentCategories($tenantId);
        $branches = $this->categoryService->getTenantBranches($tenantId);
        $stats = $this->categoryService->getCategoryStats($tenantId);

        return view('modules.menu-management.categories', compact('categories', 'parentCategories', 'branches', 'stats'));
    }

    /**
     * Store a new category (Main or Sub)
     */
    public function store(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();
        $tenantId = $user->tenant_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_categories', 'id')->where(fn($query) => $query->where('tenant_id', $tenantId)),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn($query) => $query->where('tenant_id', $tenantId)),
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

        $validator->after(function ($validator) use ($request, $tenantId) {
            $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

            $existsQuery = MenuCategory::query()
                ->where('tenant_id', $tenantId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $request->input('name'))]);

            if ($branchId === null) {
                $existsQuery->whereNull('branch_id');
            } else {
                $existsQuery->where('branch_id', $branchId);
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
        $validated['branch_id'] = $validated['branch_id'] ?? null;
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

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
            ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('menu_categories', 'id')->where(fn($query) => $query->where('tenant_id', $tenantId)),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('branches', 'id')->where(fn($query) => $query->where('tenant_id', $tenantId)),
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

        $validator->after(function ($validator) use ($request, $tenantId, $category) {
            if ((int) $request->input('parent_id') === (int) $category->id) {
                $validator->errors()->add('parent_id', 'Category cannot be its own parent.');
                return;
            }

            $branchId = $request->filled('branch_id') ? (int) $request->input('branch_id') : null;

            $existsQuery = MenuCategory::query()
                ->where('tenant_id', $tenantId)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower((string) $request->input('name'))])
                ->where('id', '!=', $category->id);

            if ($branchId === null) {
                $existsQuery->whereNull('branch_id');
            } else {
                $existsQuery->where('branch_id', $branchId);
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
        $validated['branch_id'] = $validated['branch_id'] ?? null;
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

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
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

        $category = MenuCategory::query()
            ->where('tenant_id', $tenantId)
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
