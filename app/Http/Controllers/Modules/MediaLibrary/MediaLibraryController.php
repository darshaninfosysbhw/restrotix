<?php

namespace App\Http\Controllers\Modules\MediaLibrary;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $tenantId = session('active_tenant_id') ?? $user?->tenant_id;
        $search = trim((string) $request->input('search', ''));
        $selectedCategory = trim((string) $request->input('category', 'all')) ?: 'all';

        $baseAssets = MediaAsset::query()
            ->forTenant($tenantId)
            ->active()
            ->latest()
            ->get()
            ->map(function (MediaAsset $asset) {
                return $this->transformAsset($asset);
            });

        if ($baseAssets->isEmpty()) {
            $baseAssets = $this->demoAssets();
        }

        $assets = $baseAssets->filter(function (array $asset) use ($search, $selectedCategory) {
            $matchesCategory = $selectedCategory === 'all' || $selectedCategory === '' || $asset['category_slug'] === $selectedCategory;

            if ($search === '') {
                return $matchesCategory;
            }

            $term = Str::of($search)->lower()->toString();
            $haystack = collect([
                $asset['title'] ?? '',
                $asset['slug'] ?? '',
                $asset['category_slug'] ?? '',
                implode(' ', $asset['tags'] ?? []),
            ])->join(' ');

            return $matchesCategory && Str::of($haystack)->lower()->contains($term);
        })->values();

        $categories = $this->buildCategories($baseAssets);
        $stats = $this->buildStats($baseAssets);

        return view('modules.media-library.index', compact('assets', 'categories', 'stats', 'search', 'selectedCategory'));
    }

    private function transformAsset(MediaAsset $asset): array
    {
        return [
            'id' => $asset->id,
            'title' => $asset->title,
            'slug' => $asset->slug,
            'category_slug' => $asset->category_slug,
            'category_label' => $asset->category_label,
            'source_type' => $asset->source_type,
            'thumbnail_url' => $asset->thumbnail_url,
            'file_url' => $asset->file_url,
            'mime_type' => $asset->mime_type,
            'size_label' => $asset->size_kb ? number_format($asset->size_kb) . ' KB' : '—',
            'dimensions' => ($asset->width && $asset->height) ? $asset->width . ' × ' . $asset->height : 'Responsive',
            'tags' => collect($asset->tags ?? [])->filter()->values()->all(),
            'usage_count' => $asset->usage_count,
            'is_featured' => (bool) $asset->is_featured,
            'is_active' => (bool) $asset->is_active,
            'badge' => $this->categoryBadge($asset->category_slug),
            'accent' => $this->categoryAccent($asset->category_slug),
        ];
    }

    private function demoAssets(): Collection
    {
        return collect([
            $this->demoAsset(1, 'Burger Hero Banner', 'burger', 'system', ['hero', 'menu', 'burger'], true),
            $this->demoAsset(2, 'Pizza Slice Poster', 'pizza', 'system', ['pizza', 'family', 'featured'], true),
            $this->demoAsset(3, 'Cold Drinks Promo', 'drinks', 'uploaded', ['beverage', 'refreshing', 'summer']),
            $this->demoAsset(4, 'Dessert Mood Board', 'desserts', 'system', ['sweet', 'cake', 'plated']),
            $this->demoAsset(5, 'Breakfast Spotlight', 'breakfast', 'uploaded', ['morning', 'combo', 'hot']),
            $this->demoAsset(6, 'Table QR Poster', 'table-qr', 'system', ['qr', 'scan', 'table'], true),
            $this->demoAsset(7, 'Combo Offer Card', 'promotions', 'system', ['deal', 'combo', 'save']),
            $this->demoAsset(8, 'Starter Platter', 'main-course', 'uploaded', ['spicy', 'party', 'meal']),
        ]);
    }

    private function demoAsset(int $id, string $title, string $categorySlug, string $sourceType, array $tags = [], bool $featured = false): array
    {
        return [
            'id' => $id,
            'title' => $title,
            'slug' => Str::slug($title),
            'category_slug' => $categorySlug,
            'category_label' => Str::of($categorySlug)->replace(['_', '-'], ' ')->headline()->toString(),
            'source_type' => $sourceType,
            'thumbnail_url' => null,
            'file_url' => null,
            'mime_type' => 'image/png',
            'size_label' => $sourceType === 'system' ? '42 KB' : '68 KB',
            'dimensions' => '1080 × 1350',
            'tags' => $tags,
            'usage_count' => $featured ? 24 : 8,
            'is_featured' => $featured,
            'is_active' => true,
            'badge' => $this->categoryBadge($categorySlug),
            'accent' => $this->categoryAccent($categorySlug),
        ];
    }

    private function buildCategories(Collection $assets): Collection
    {
        $seed = collect([
            'all' => 'All',
            'burger' => 'Burger',
            'pizza' => 'Pizza',
            'drinks' => 'Drinks',
            'desserts' => 'Desserts',
            'breakfast' => 'Breakfast',
            'main-course' => 'Main Course',
            'table-qr' => 'QR Posters',
            'promotions' => 'Promotions',
        ]);

        $dynamic = $assets->pluck('category_slug')
            ->filter()
            ->unique()
            ->mapWithKeys(fn ($slug) => [$slug => Str::of($slug)->replace(['_', '-'], ' ')->headline()->toString()]);

        return $seed->merge($dynamic)->unique();
    }

    private function buildStats(Collection $assets): array
    {
        return [
            'total' => $assets->count(),
            'system' => $assets->where('source_type', 'system')->count(),
            'uploaded' => $assets->where('source_type', 'uploaded')->count(),
            'featured' => $assets->where('is_featured', true)->count(),
        ];
    }

    private function categoryBadge(string $slug): string
    {
        return match ($slug) {
            'burger' => 'Burger Focus',
            'pizza' => 'Pizza Set',
            'drinks' => 'Cold Drinks',
            'desserts' => 'Sweet Menu',
            'breakfast' => 'Breakfast',
            'table-qr' => 'QR Poster',
            'promotions' => 'Promo Card',
            default => 'Template',
        };
    }

    private function categoryAccent(string $slug): string
    {
        return match ($slug) {
            'burger' => 'from-orange-500/80 via-amber-500/70 to-rose-500/40',
            'pizza' => 'from-red-500/70 via-orange-500/70 to-yellow-500/40',
            'drinks' => 'from-sky-500/70 via-cyan-500/70 to-emerald-500/40',
            'desserts' => 'from-fuchsia-500/70 via-pink-500/70 to-orange-500/40',
            'breakfast' => 'from-amber-500/70 via-orange-500/60 to-rose-500/40',
            'table-qr' => 'from-gray-700 via-gray-800 to-black',
            'promotions' => 'from-violet-500/70 via-indigo-500/70 to-sky-500/40',
            default => 'from-gray-700 via-gray-800 to-slate-900',
        };
    }
}
