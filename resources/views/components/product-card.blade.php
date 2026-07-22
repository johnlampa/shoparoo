@props(['product'])

@php
    $discount = $product->discount_percent;
@endphp

<div
    x-data="productItem({{ json_encode([
        'id' => $product->id,
        'slug' => $product->slug,
        'image' => $product->image ?: '/img/noimage.png',
        'title' => $product->title,
        'price' => $product->price,
        'addToCartUrl' => route('cart.add', $product),
    ]) }})"
    class="group relative flex flex-col bg-white border border-ink-200/80 hover:border-brand-400 hover:shadow-lift transition-all duration-300"
>
    @if ($discount)
        <span class="discount-badge">-{{ $discount }}%</span>
    @endif

    <a href="{{ route('product.view', $product->slug) }}" class="relative block overflow-hidden bg-ink-50 aspect-square">
        <img
            :src="product.image"
            alt="{{ $product->title }}"
            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
            loading="lazy"
        />
    </a>

    <div class="flex flex-1 flex-col gap-2 p-3">
        <h3 class="text-sm leading-snug text-ink-800 line-clamp-2 min-h-[2.5rem]">
            <a href="{{ route('product.view', $product->slug) }}" class="hover:text-brand-600 transition-colors">
                {{ $product->title }}
            </a>
        </h3>

        <div class="mt-auto">
            <div class="flex items-baseline gap-2 flex-wrap">
                <span class="price-now text-lg">${{ number_format($product->price, 2) }}</span>
                @if ($product->compare_at_price && $product->compare_at_price > $product->price)
                    <span class="price-was">${{ number_format($product->compare_at_price, 2) }}</span>
                @endif
            </div>
        </div>

        <button
            type="button"
            class="btn-primary w-full text-sm mt-1 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity"
            @click="addToCart()"
        >
            Add to Cart
        </button>
    </div>
</div>
