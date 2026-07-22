<?php
/** @var \Illuminate\Pagination\LengthAwarePaginator $products */
/** @var bool $isHome */
/** @var \Illuminate\Support\Collection $flashSaleProducts */
/** @var \Illuminate\Support\Collection $topCategories */

$categoryList = \App\Models\Category::getActiveAsTree();
$isHome = $isHome ?? false;
$flashSaleProducts = $flashSaleProducts ?? collect();
$topCategories = $topCategories ?? collect();
?>

<x-app-layout>
    <x-category-list :category-list="$categoryList" class="mb-4"/>

    @if ($isHome)
        {{-- Hero --}}
        <section
            class="relative overflow-hidden rounded-2xl mb-6 min-h-[220px] sm:min-h-[300px] animate-fade-up"
            style="background:
                linear-gradient(120deg, rgba(15,23,42,0.92) 0%, rgba(15,23,42,0.55) 45%, rgba(245,114,36,0.35) 100%),
                url('https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1600&h=700&fit=crop') center/cover;"
        >
            <div class="relative z-10 flex flex-col justify-end h-full min-h-[220px] sm:min-h-[300px] p-6 sm:p-10">
                <p class="font-display text-4xl sm:text-6xl font-extrabold text-white tracking-tight drop-shadow-sm">
                    Shoparoo
                </p>
                <p class="mt-2 max-w-md text-base sm:text-lg text-white/85">
                    Big deals. Everyday essentials. Shop smarter — just for you.
                </p>
                <div class="mt-5 flex flex-wrap gap-3">
                    <a href="#just-for-you" class="btn-primary px-6 py-2.5 shadow-lift">Shop Now</a>
                    <a href="#flash-sale" class="btn-ghost bg-white/15 text-white border-white/30 hover:bg-white/25 hover:text-white">
                        Flash Sale
                    </a>
                </div>
            </div>
        </section>

        {{-- Category shortcuts --}}
        @if ($topCategories->isNotEmpty())
            <section class="mb-6 animate-slide-in" style="animation-delay: 80ms">
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-2 sm:gap-3">
                    @foreach ($topCategories as $cat)
                        <a
                            href="{{ route('byCategory', $cat) }}"
                            class="flex flex-col items-center justify-center gap-2 rounded-xl bg-white border border-ink-200/70 px-3 py-4 text-center hover:border-brand-400 hover:shadow-soft transition-all group"
                        >
                            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-brand-50 text-brand-600 font-display font-bold text-lg group-hover:bg-brand-500 group-hover:text-white transition-colors">
                                {{ mb_substr($cat->name, 0, 1) }}
                            </span>
                            <span class="text-xs sm:text-sm font-medium text-ink-700 leading-tight">{{ $cat->name }}</span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        {{-- Flash Sale --}}
        @if ($flashSaleProducts->isNotEmpty())
            <section id="flash-sale" class="section-shell mb-6 p-4 sm:p-5 animate-fade-up" style="animation-delay: 120ms"
                     x-data="{
                        end: Date.now() + 1000 * 60 * 60 * 8,
                        h: '00', m: '00', s: '00',
                        tick() {
                            const diff = Math.max(0, this.end - Date.now());
                            const total = Math.floor(diff / 1000);
                            this.h = String(Math.floor(total / 3600)).padStart(2, '0');
                            this.m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
                            this.s = String(total % 60).padStart(2, '0');
                        },
                        init() {
                            this.tick();
                            setInterval(() => this.tick(), 1000);
                        }
                     }"
            >
                <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                    <div class="flex items-center gap-3">
                        <h2 class="font-display text-xl sm:text-2xl font-bold text-ink-900">
                            Flash Sale
                        </h2>
                        <div class="flex items-center gap-1 text-sm font-semibold">
                            <span class="text-slate-500 hidden sm:inline">Ending in</span>
                            <span class="bg-ink-900 text-white rounded px-1.5 py-0.5 tabular-nums" x-text="h"></span>
                            <span class="text-ink-900">:</span>
                            <span class="bg-ink-900 text-white rounded px-1.5 py-0.5 tabular-nums" x-text="m"></span>
                            <span class="text-ink-900">:</span>
                            <span class="bg-ink-900 text-white rounded px-1.5 py-0.5 tabular-nums animate-pulse-soft" x-text="s"></span>
                        </div>
                    </div>
                    <a href="#just-for-you" class="text-sm font-semibold text-brand-600 hover:text-brand-700">
                        Shop all products →
                    </a>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3">
                    @foreach ($flashSaleProducts as $product)
                        <x-product-card :product="$product"/>
                    @endforeach
                </div>
            </section>
        @endif
    @endif

    {{-- Just For You / listing --}}
    <section id="just-for-you" class="section-shell p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 mb-4"
             x-data="{
                selectedSort: '{{ request()->get('sort', '-updated_at') }}',
                searchKeyword: '{{ request()->get('search') }}',
                updateUrl() {
                    const params = new URLSearchParams(window.location.search)
                    if (this.selectedSort && this.selectedSort !== '-updated_at') {
                        params.set('sort', this.selectedSort)
                    } else {
                        params.delete('sort')
                    }
                    if (this.searchKeyword) {
                        params.set('search', this.searchKeyword)
                    } else {
                        params.delete('search')
                    }
                    window.location.href = window.location.origin + window.location.pathname + '?' + params.toString();
                }
             }"
        >
            <h2 class="font-display text-xl sm:text-2xl font-bold text-ink-900 shrink-0">
                {{ $isHome ? 'Just For You' : 'Products' }}
            </h2>

            <form action="" method="GET" class="flex-1 sm:max-w-xs" @submit.prevent="updateUrl">
                <x-input type="text" name="search" placeholder="Filter this page…" x-model="searchKeyword"/>
            </form>

            <x-input
                x-model="selectedSort"
                @change="updateUrl"
                type="select"
                name="sort"
                class="w-full sm:w-56 focus:border-brand-500 focus:ring-brand-500 border-gray-300 rounded"
            >
                <option value="price">Price (Low → High)</option>
                <option value="-price">Price (High → Low)</option>
                <option value="title">Name (A → Z)</option>
                <option value="-title">Name (Z → A)</option>
                <option value="-updated_at">Newest first</option>
                <option value="updated_at">Oldest first</option>
            </x-input>
        </div>

        @if ($products->count() === 0)
            <div class="text-center text-slate-500 py-16 text-lg">
                No products found. Try another search or category.
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                @foreach ($products as $product)
                    <x-product-card :product="$product"/>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $products->appends(['sort' => request('sort'), 'search' => request('search')])->links() }}
            </div>
        @endif
    </section>
</x-app-layout>
