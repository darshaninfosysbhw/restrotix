<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MenuItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'branch_id',
        'category_id',
        'name',
        'slug',
        'code',
        'description',
        'image',
        'base_price',
        'sale_price',
        'tax_percent',
        'type',
        'has_variants',
        'is_available',
        'is_active',
        'is_recommended',
        'sort_order'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'tax_percent' => 'decimal:2',
        'is_available' => 'boolean',
        'is_active' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    // 🚀 Auto-slug and Code generation
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            if (empty($item->slug)) {
                $item->slug = Str::slug($item->name) . '-' . rand(100, 999);
            }

            // Auto item code generation when code is not provided.
            // Example: ITM-4729 (unique per tenant, including soft-deleted rows).
            if (empty($item->code)) {
                $item->code = static::generateUniqueCode((int) $item->tenant_id);
            }
        });
    }

    private static function generateUniqueCode(int $tenantId): string
    {
        $attempts = 0;

        do {
            $code = 'ITM-' . random_int(1000, 9999);
            $exists = static::withTrashed()
                ->where('tenant_id', $tenantId)
                ->where('code', $code)
                ->exists();

            $attempts++;
        } while ($exists && $attempts < 20);

        if ($exists) {
            $code = 'ITM-' . strtoupper(Str::random(6));
        }

        return $code;
    }

    // 🌳 Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // Item ke paas bohot saare variants (Half/Full) ho sakte hain
    public function variants(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItemVariant::class, 'menu_item_id');
    }

    // Item ke paas bohot saare active addons ho sakte hain
    public function addons(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(MenuItemAddon::class, 'menu_item_id')->where('is_active', true);
    }
}
