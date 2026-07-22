@props(['categoryList'])

<div {{ $attributes->merge(['class' => 'category-list flex flex-wrap text-sm bg-ink-800 text-white rounded-lg overflow-hidden shadow-soft']) }}>
    @if (!empty($categoryList))
        @foreach($categoryList as $category)
            <div class="category-item relative">
                <a href="{{ route('byCategory', $category) }}" class="cursor-pointer block py-2.5 px-4 hover:bg-brand-500/90 transition-colors whitespace-nowrap">
                    {{ $category->name }}
                </a>
                <x-category-list class="absolute left-0 top-[100%] z-50 hidden flex-col min-w-[180px] rounded-b-lg shadow-lift" :category-list="$category->children"/>
            </div>
        @endforeach
    @endif
</div>
