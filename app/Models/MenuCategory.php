<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MenuCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'parent_id',
        'name',
        'slug',
        'code',
        'image',
        'sort_order',
        'is_active'
    ];

    // 🚀 Boot Method: Automatic Logic
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            // 1. Auto-Slug Generation
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }

            // 2. Auto-Code Generation (If empty)
            // Example: BEVERAGES -> BEV-1234
            if (empty($category->code)) {
                $prefix = strtoupper(substr($category->name, 0, 3));
                $category->code = $prefix . '-' . rand(1000, 9999);
            }
        });
    }

    // 🌳 Relationship: Parent Category (Sub-category belongs to a Parent)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'parent_id');
    }

    // 🌳 Relationship: Child Categories (Parent has many Sub-categories)
    public function children(): HasMany
    {
        return $this->hasMany(MenuCategory::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    // 🏢 Relationship: Branch Mapping
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // 🏷️ Relationship: Menu Items (Is category mein kitne dishes hain)
    public function items(): HasMany
    {
        return $this->hasMany(MenuItem::class, 'category_id');
    }
}
