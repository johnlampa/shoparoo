<header
    x-data="{
        mobileMenuOpen: false,
        cartItemsCount: {{ \App\Helpers\Cart::getCartItemsCount() }},
    }"
    @cart-change.window="cartItemsCount = $event.detail.count"
    class="sticky top-0 z-40 border-b border-ink-900/10 bg-ink-900 text-white shadow-soft"
>
    <div class="mx-auto max-w-7xl px-3 sm:px-5">
        <div class="flex items-center gap-3 sm:gap-6 py-3">
            <a href="{{ route('home') }}" class="shrink-0 font-display text-2xl sm:text-3xl font-extrabold tracking-tight text-white hover:text-brand-300 transition-colors">
                Shoparoo
            </a>

            <form action="{{ route('home') }}" method="GET" class="hidden sm:flex flex-1 max-w-2xl">
                <div class="relative w-full flex">
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search products, brands, and more…"
                        class="w-full rounded-l-md border-0 py-2.5 pl-4 pr-3 text-ink-800 placeholder:text-slate-400 focus:ring-2 focus:ring-brand-400"
                    />
                    <button type="submit" class="rounded-r-md bg-brand-500 px-5 font-semibold hover:bg-brand-600 transition-colors">
                        Search
                    </button>
                </div>
            </form>

            <nav class="hidden md:flex items-center ml-auto gap-1">
                <a href="{{ route('cart.index') }}" class="relative inline-flex items-center gap-2 rounded-md px-3 py-2 hover:bg-white/10 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="text-sm font-medium">Cart</span>
                    <small
                        x-show="cartItemsCount"
                        x-transition
                        x-cloak
                        x-text="cartItemsCount"
                        class="absolute -top-1 -right-1 min-w-[1.25rem] text-center py-0.5 px-1.5 rounded-full bg-brand-500 text-xs font-bold"
                    ></small>
                </a>

                @if (!Auth::guest())
                    <div x-data="{open: false}" class="relative">
                        <button
                            type="button"
                            @click="open = !open"
                            class="inline-flex items-center gap-2 rounded-md px-3 py-2 hover:bg-white/10 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span class="text-sm font-medium">Account</span>
                        </button>
                        <ul
                            @click.outside="open = false"
                            x-show="open"
                            x-transition
                            x-cloak
                            class="absolute right-0 mt-1 w-48 rounded-lg bg-ink-800 py-2 shadow-lift ring-1 ring-white/10"
                        >
                            <li><a href="{{ route('profile') }}" class="block px-4 py-2 text-sm hover:bg-white/10">My Profile</a></li>
                            <li><a href="{{ route('order.index') }}" class="block px-4 py-2 text-sm hover:bg-white/10">My Orders</a></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}"
                                       class="block px-4 py-2 text-sm hover:bg-white/10"
                                       onclick="event.preventDefault(); this.closest('form').submit();">
                                        Log Out
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="rounded-md px-3 py-2 text-sm font-medium hover:bg-white/10 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="ml-1 rounded-md bg-brand-500 px-3 py-2 text-sm font-semibold hover:bg-brand-600 transition-colors">
                        Register
                    </a>
                @endif
            </nav>

            <button @click="mobileMenuOpen = !mobileMenuOpen" class="ml-auto p-2 md:hidden rounded-md hover:bg-white/10" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('home') }}" method="GET" class="sm:hidden pb-3">
            <div class="flex">
                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search Shoparoo…"
                    class="w-full rounded-l-md border-0 py-2 pl-3 text-ink-800 text-sm focus:ring-2 focus:ring-brand-400"
                />
                <button type="submit" class="rounded-r-md bg-brand-500 px-4 text-sm font-semibold">Go</button>
            </div>
        </form>
    </div>

    {{-- Mobile drawer --}}
    <div
        class="fixed inset-0 z-50 md:hidden"
        x-show="mobileMenuOpen"
        x-cloak
    >
        <div class="absolute inset-0 bg-black/50" @click="mobileMenuOpen = false"></div>
        <div
            class="absolute top-0 bottom-0 w-[260px] bg-ink-900 shadow-lift transition-transform"
            :class="mobileMenuOpen ? 'left-0' : '-left-[260px]'"
        >
            <div class="p-4 border-b border-white/10 font-display text-xl font-bold">Shoparoo</div>
            <ul class="py-2">
                <li>
                    <a href="{{ route('cart.index') }}" class="flex items-center justify-between px-4 py-3 hover:bg-white/10">
                        <span>Cart</span>
                        <small x-show="cartItemsCount" x-text="cartItemsCount" x-cloak class="rounded-full bg-brand-500 px-2 py-0.5 text-xs"></small>
                    </a>
                </li>
                @if (!Auth::guest())
                    <li><a href="{{ route('profile') }}" class="block px-4 py-3 hover:bg-white/10">My Profile</a></li>
                    <li><a href="{{ route('order.index') }}" class="block px-4 py-3 hover:bg-white/10">My Orders</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <a href="{{ route('logout') }}" class="block px-4 py-3 hover:bg-white/10"
                               onclick="event.preventDefault(); this.closest('form').submit();">Log Out</a>
                        </form>
                    </li>
                @else
                    <li><a href="{{ route('login') }}" class="block px-4 py-3 hover:bg-white/10">Login</a></li>
                    <li class="px-4 py-3">
                        <a href="{{ route('register') }}" class="block text-center bg-brand-500 rounded-md py-2 font-semibold">Register</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</header>
