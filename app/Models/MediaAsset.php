<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'created_by',
        'title',
        'slug',
        'category_slug',
        'source_type',
        'file_path',
        'thumbnail_path',
        'mime_type',
        'size_kb',
        'width',
        'height',
        'tags',
        'usage_count',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'tenant_id' => 'integer',
        'created_by' => 'integer',
        'size_kb' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
        'usage_count' => 'integer',
        'tags' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'file_url',
        'thumbnail_url',
        'category_label',
    ];

    public function getFileUrlAttribute(): ?string
    {
        if (!$this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return Storage::disk('public')->url($this->thumbnail_path);
        }

        return $this->file_url;
    }

    public function getCategoryLabelAttribute(): string
    {
        return Str::of((string) $this->category_slug)
            ->replace(['_', '-'], ' ')
            ->headline()
            ->toString();
    }

    public function scopeForTenant($query, ?int $tenantId)
    {
        return $query->when(
            $tenantId,
            fn ($builder) => $builder->where(function ($nested) use ($tenantId) {
                $nested->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            }),
            fn ($builder) => $builder->whereNull('tenant_id')
        );
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
