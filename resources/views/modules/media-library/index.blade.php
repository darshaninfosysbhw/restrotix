@extends('core.layouts.admin')

@section('content')
    @php
        $galleryAssets = collect($assets ?? []);
        $categoryList = collect($categories ?? []);
        $activeCategory = $selectedCategory ?? 'all';
        $firstAsset = $galleryAssets->first();
    @endphp

    <style>
        .media-card {
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .media-card:hover {
            transform: translateY(-2px);
        }

        .media-card.is-active {
            border-color: rgba(249, 115, 22, 0.9);
            box-shadow: 0 20px 40px rgba(249, 115, 22, 0.16);
        }

        .media-gradient {
            background:
                radial-gradient(circle at 20% 20%, rgba(255, 255, 255, 0.16), transparent 35%),
                radial-gradient(circle at 80% 0%, rgba(255, 255, 255, 0.14), transparent 30%),
                linear-gradient(135deg, rgba(17, 24, 39, 0.92), rgba(31, 41, 55, 0.86));
        }
    </style>

    <div class="flex-1 overflow-y-auto p-6 bg-gray-900 space-y-6">
        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                <div class="space-y-2">
                    <p class="text-xs uppercase tracking-[0.25em] text-gray-400">Menu Management / Gallery</p>
                    <h1 class="text-3xl md:text-4xl font-bold text-white">Media Library</h1>
                    <p class="text-sm md:text-base text-gray-400 max-w-3xl">
                        Choose ready-made food visuals, QR posters, promo cards, and menu art from one place.
                        Upload integration will plug into the menu item form in the next step.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="#media-gallery"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-orange-500/30 bg-orange-500/10 text-orange-500 hover:bg-orange-500/20 transition">
                        <i class="fas fa-images"></i>
                        Browse Gallery
                    </a>
                    <button type="button"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 hover:bg-cyan-500/20 transition cursor-pointer">
                        <i class="fas fa-cloud-arrow-up"></i>
                        Upload Flow Next
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Total Assets</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-orange-500/15 text-orange-500 flex items-center justify-center">
                        <i class="fas fa-layer-group"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">System Templates</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['system'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-violet-500/15 text-violet-400 flex items-center justify-center">
                        <i class="fas fa-rectangle-list"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Uploaded From Local</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['uploaded'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-sky-500/15 text-sky-400 flex items-center justify-center">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
            <div class="bg-gray-800 border border-gray-700 rounded-2xl p-4">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs text-gray-400 uppercase tracking-wide">Featured</p>
                        <p class="text-2xl font-bold text-white mt-1">{{ $stats['featured'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-emerald-500/15 text-emerald-400 flex items-center justify-center">
                        <i class="fas fa-star"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-gray-800 border border-gray-700 rounded-2xl p-5">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm text-gray-400">Quick Filters</span>
                    <span class="px-2.5 py-1 rounded-full text-xs bg-orange-500/10 text-orange-500 border border-orange-500/30">
                        Total : {{ $stats['total'] ?? 0 }}
                    </span>
                </div>

                <div class="flex flex-col lg:flex-row lg:items-center gap-3 w-full lg:w-auto">
                    <div class="relative w-full lg:w-96">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input id="mediaSearchInput" type="text" value="{{ $search ?? '' }}"
                            placeholder="Search image, category, or tag..."
                            class="w-full bg-gray-900 border border-gray-700 rounded-xl pl-9 pr-4 py-2.5 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-1 focus:ring-orange-500/60">
                    </div>

                    <div class="flex flex-wrap items-center gap-2" id="mediaCategoryFilters">
                        @foreach ($categoryList as $slug => $label)
                            <button type="button" data-category-filter="{{ $slug }}"
                                class="px-3 py-2 rounded-xl text-xs border transition {{ ($activeCategory === $slug || ($slug === 'all' && ($activeCategory === 'all' || $activeCategory === ''))) ? 'bg-orange-500 text-white border-orange-500 shadow-lg shadow-orange-500/20' : 'bg-white/5 hover:bg-white/10 text-gray-300 border-white/10' }}">
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div id="media-gallery" class="grid grid-cols-1 xl:grid-cols-[minmax(0,1.65fr)_380px] gap-6">
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-400">Gallery Results</p>
                        <h2 class="text-xl font-semibold text-white">Ready to use visuals</h2>
                    </div>
                    <p id="mediaResultCount" class="text-xs text-gray-400">
                        Showing {{ $galleryAssets->count() }} assets
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 2xl:grid-cols-3 gap-4">
                    @forelse ($galleryAssets as $index => $asset)
                        <button type="button" data-media-card data-asset-index="{{ $index }}"
                            data-title="{{ strtolower($asset['title']) }}"
                            data-slug="{{ strtolower($asset['slug']) }}"
                            data-category="{{ $asset['category_slug'] }}"
                            data-tags="{{ implode(',', $asset['tags'] ?? []) }}"
                            class="media-card text-left rounded-2xl border border-gray-700 bg-gray-800 overflow-hidden group">
                            <div class="relative p-4">
                                <div class="media-gradient rounded-[1.25rem] border border-white/10 shadow-[0_24px_60px_rgba(0,0,0,0.35)] overflow-hidden min-h-[250px] flex flex-col justify-between">
                                    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.16),transparent_25%),radial-gradient(circle_at_80%_0%,rgba(255,255,255,0.12),transparent_24%),linear-gradient(180deg,rgba(255,255,255,0.02),transparent)]"></div>

                                    <div class="relative z-10 flex items-start justify-between gap-3 p-4">
                                        <div class="space-y-1">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-[0.2em] bg-white/10 text-white border border-white/10">
                                                {{ $asset['badge'] }}
                                            </span>
                                            <p class="text-[11px] text-white/70 uppercase tracking-[0.18em]">
                                                {{ $asset['source_type'] }} asset
                                            </p>
                                        </div>

                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] font-medium {{ $asset['is_featured'] ? 'bg-amber-400/20 text-amber-200 border border-amber-400/30' : 'bg-white/10 text-white/70 border border-white/10' }}">
                                            {{ $asset['is_featured'] ? 'Featured' : 'Template' }}
                                        </span>
                                    </div>

                                    <div class="relative z-10 p-4 pb-5">
                                        <div class="rounded-[1.1rem] border border-white/10 bg-black/20 backdrop-blur-sm p-4">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="space-y-1">
                                                    <p class="text-xs text-white/60 uppercase tracking-[0.24em]">Media
                                                        Poster</p>
                                                    <h3 class="text-xl font-black text-white leading-tight">
                                                        {{ $asset['title'] }}
                                                    </h3>
                                                </div>
                                                <div
                                                    class="w-12 h-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-white">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            </div>

                                            <div class="mt-4 grid grid-cols-2 gap-3 text-xs text-white/75">
                                                <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                                    <span class="block text-[10px] uppercase tracking-[0.18em] text-white/45">Size</span>
                                                    {{ $asset['size_label'] }}
                                                </div>
                                                <div class="rounded-xl bg-white/5 border border-white/10 px-3 py-2">
                                                    <span class="block text-[10px] uppercase tracking-[0.18em] text-white/45">Usage</span>
                                                    {{ $asset['usage_count'] }} times
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="relative z-10 px-4 pb-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] border border-white/10 bg-white/5 text-white/80">
                                                <i class="fas fa-tag text-[10px]"></i>
                                                {{ $asset['category_label'] }}
                                            </span>
                                            <span
                                                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] border border-white/10 bg-white/5 text-white/80">
                                                <i class="fas fa-ruler-combined text-[10px]"></i>
                                                {{ $asset['dimensions'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="px-4 pb-4 space-y-3">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-white">{{ $asset['title'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $asset['slug'] }}</p>
                                    </div>
                                    <span
                                        class="px-2.5 py-1 rounded-full text-[10px] uppercase tracking-[0.14em] border {{ $asset['source_type'] === 'system' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-sky-500/10 text-sky-400 border-sky-500/20' }}">
                                        {{ $asset['source_type'] }}
                                    </span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @foreach (array_slice($asset['tags'] ?? [], 0, 3) as $tag)
                                        <span
                                            class="px-2.5 py-1 rounded-full text-[10px] bg-white/5 text-gray-300 border border-white/10">
                                            #{{ $tag }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </button>
                    @empty
                        <div class="col-span-full bg-gray-800 border border-dashed border-gray-700 rounded-2xl p-10 text-center">
                            <i class="fas fa-images text-3xl text-gray-500"></i>
                            <h3 class="text-white font-semibold mt-4">No assets found</h3>
                            <p class="text-gray-400 text-sm mt-2">When you upload media, it will appear here as a reusable
                                gallery.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="lg:sticky lg:top-6 self-start bg-gray-800 border border-gray-700 rounded-2xl p-5">
                <div class="flex items-center justify-between gap-3 mb-4">
                    <div>
                        <p class="text-xs uppercase tracking-[0.22em] text-gray-400">Selected Asset</p>
                        <h3 id="mediaPreviewTitle" class="text-xl font-bold text-white mt-1">
                            {{ $firstAsset['title'] ?? 'Pick a template' }}
                        </h3>
                    </div>
                    <span id="mediaPreviewBadge"
                        class="px-2.5 py-1 rounded-full text-[10px] font-semibold uppercase tracking-[0.14em] border border-orange-500/30 bg-orange-500/10 text-orange-400">
                        {{ $firstAsset['badge'] ?? 'Template' }}
                    </span>
                </div>

                <div id="mediaPreviewCanvas"
                    class="media-gradient rounded-[1.5rem] border border-white/10 min-h-[360px] p-5 flex flex-col justify-between overflow-hidden">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-xs text-white/60 uppercase tracking-[0.22em]">Gallery Preview</p>
                            <p id="mediaPreviewCategory" class="text-white font-semibold mt-1">
                                {{ $firstAsset['category_label'] ?? 'Ready to browse' }}
                            </p>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center text-white">
                            <i class="fas fa-photo-film text-lg"></i>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-2xl bg-black/25 border border-white/10 p-4 backdrop-blur-sm">
                            <p class="text-[11px] text-white/55 uppercase tracking-[0.22em]">Preview Style</p>
                            <h4 id="mediaPreviewSubtitle" class="text-2xl font-black text-white mt-2 leading-tight">
                                {{ $firstAsset['title'] ?? 'Select an image from the gallery' }}
                            </h4>
                            <p id="mediaPreviewMeta" class="text-sm text-white/70 mt-2">
                                {{ $firstAsset ? ($firstAsset['size_label'] . ' · ' . $firstAsset['dimensions']) : 'Reusable visuals for menu items and QR posters' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-3 text-sm text-white/80">
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                                <p class="text-[10px] text-white/45 uppercase tracking-[0.18em]">Source</p>
                                <p id="mediaPreviewSource" class="mt-1 font-semibold">{{ $firstAsset['source_type'] ?? 'system' }}</p>
                            </div>
                            <div class="rounded-2xl bg-white/5 border border-white/10 p-3">
                                <p class="text-[10px] text-white/45 uppercase tracking-[0.18em]">Used</p>
                                <p id="mediaPreviewUsage" class="mt-1 font-semibold">{{ $firstAsset['usage_count'] ?? 0 }} times</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        <button type="button" id="useSelectedAssetBtn"
                            class="w-full inline-flex items-center justify-center gap-2 rounded-xl px-4 py-3 bg-orange-500 text-white font-semibold shadow-lg shadow-orange-500/20 hover:bg-orange-400 transition cursor-pointer">
                            <i class="fas fa-circle-check"></i>
                            Use this image
                        </button>
                        <div class="rounded-2xl border border-dashed border-orange-500/30 bg-orange-500/5 px-4 py-3">
                            <p class="text-sm text-orange-200 font-medium">Next step flow</p>
                            <p class="text-xs text-orange-100/80 mt-1">
                                This gallery is now connected to admin sidebar. Next we wire the upload picker inside the menu
                                item create/edit forms.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const assets = @json($galleryAssets->values());
            const cards = Array.from(document.querySelectorAll('[data-media-card]'));
            const searchInput = document.getElementById('mediaSearchInput');
            const categoryButtons = Array.from(document.querySelectorAll('[data-category-filter]'));
            const resultCount = document.getElementById('mediaResultCount');
            const previewTitle = document.getElementById('mediaPreviewTitle');
            const previewBadge = document.getElementById('mediaPreviewBadge');
            const previewCategory = document.getElementById('mediaPreviewCategory');
            const previewSubtitle = document.getElementById('mediaPreviewSubtitle');
            const previewMeta = document.getElementById('mediaPreviewMeta');
            const previewSource = document.getElementById('mediaPreviewSource');
            const previewUsage = document.getElementById('mediaPreviewUsage');
            const useButton = document.getElementById('useSelectedAssetBtn');

            let activeCategory = @json($activeCategory);
            let activeIndex = 0;

            const applyActiveState = (card) => {
                cards.forEach(item => item.classList.remove('is-active'));
                card.classList.add('is-active');
            };

            const syncPreview = (asset) => {
                if (!asset) return;

                previewTitle.textContent = asset.title || 'Media Library';
                previewBadge.textContent = asset.badge || 'Template';
                previewCategory.textContent = asset.category_label || 'Gallery';
                previewSubtitle.textContent = asset.title || 'Selected image';
                previewMeta.textContent = `${asset.size_label || '—'} · ${asset.dimensions || 'Responsive'}`;
                previewSource.textContent = asset.source_type || 'system';
                previewUsage.textContent = `${asset.usage_count || 0} times`;
            };

            const matchesSearch = (card, term) => {
                if (!term) return true;

                const haystack = [
                    card.dataset.title || '',
                    card.dataset.slug || '',
                    card.dataset.category || '',
                    card.dataset.tags || ''
                ].join(' ').toLowerCase();

                return haystack.includes(term);
            };

            const applyFilters = () => {
                const term = (searchInput?.value || '').trim().toLowerCase();
                let visibleCount = 0;
                let firstVisibleCard = null;

                cards.forEach((card, index) => {
                    const categoryMatch = activeCategory === 'all' || activeCategory === '' || card.dataset.category === activeCategory;
                    const searchMatch = matchesSearch(card, term);
                    const isVisible = categoryMatch && searchMatch;

                    card.classList.toggle('hidden', !isVisible);

                    if (isVisible) {
                        visibleCount++;
                        if (!firstVisibleCard) firstVisibleCard = card;
                    }

                    if (index === activeIndex && isVisible) {
                        applyActiveState(card);
                    }
                });

                if (!cards.some(card => card.classList.contains('is-active') && !card.classList.contains('hidden')) && firstVisibleCard) {
                    firstVisibleCard.click();
                }

                if (resultCount) {
                    resultCount.textContent = `Showing ${visibleCount} assets`;
                }
            };

            cards.forEach((card) => {
                card.addEventListener('click', () => {
                    const index = Number(card.dataset.assetIndex || 0);
                    const asset = assets[index];

                    activeIndex = index;
                    applyActiveState(card);
                    syncPreview(asset);

                    if (window.showToast) {
                        window.showToast('success', `${asset?.title || 'Asset'} selected for preview`, 1800);
                    }
                });
            });

            categoryButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    activeCategory = button.dataset.categoryFilter || 'all';

                    categoryButtons.forEach((item) => {
                        const active = item.dataset.categoryFilter === activeCategory;
                        item.classList.toggle('bg-orange-500', active);
                        item.classList.toggle('text-white', active);
                        item.classList.toggle('border-orange-500', active);
                        item.classList.toggle('shadow-lg', active);
                        item.classList.toggle('shadow-orange-500/20', active);
                        item.classList.toggle('bg-white/5', !active);
                        item.classList.toggle('hover:bg-white/10', !active);
                        item.classList.toggle('text-gray-300', !active);
                        item.classList.toggle('border-white/10', !active);
                    });

                    applyFilters();
                });
            });

            searchInput?.addEventListener('input', applyFilters);

            useButton?.addEventListener('click', () => {
                const asset = assets[activeIndex];
                if (window.showToast) {
                    window.showToast('success', `${asset?.title || 'Asset'} ready for menu form`, 2200);
                }
            });

            if (cards.length) {
                cards[0].click();
            }

            applyFilters();
        });
    </script>
@endsection
