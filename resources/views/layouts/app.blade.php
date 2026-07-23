<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Shoparoo') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:wght@400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
@include('layouts.navigation')

<main class="flex-1 mx-auto w-full max-w-7xl px-3 sm:px-5 py-4 sm:py-6">
    {{ $slot }}
</main>

<footer class="mt-auto border-t border-ink-200/80 bg-ink-900 text-ink-100">
    <div class="mx-auto max-w-7xl px-5 py-10 grid gap-8 sm:grid-cols-3">
        <div>
            <p class="font-display text-2xl font-bold text-white tracking-tight">Shoparoo</p>
            <p class="mt-2 text-sm text-slate-400 max-w-xs">
                Effortless shopping with everyday deals, fast delivery vibes, and products picked for you.
            </p>
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-400 mb-3">Customer Care</p>
            <ul class="space-y-2 text-sm text-slate-300">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Help Center</a></li>
                <li><a href="{{ route('cart.index') }}" class="hover:text-white transition-colors">Orders &amp; Cart</a></li>
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">Shipping &amp; Delivery</a></li>
            </ul>
        </div>
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-400 mb-3">Shoparoo</p>
            <ul class="space-y-2 text-sm text-slate-300">
                <li><a href="{{ route('home') }}" class="hover:text-white transition-colors">About</a></li>
                @auth
                    <li><a href="{{ route('profile') }}" class="hover:text-white transition-colors">My Account</a></li>
                @else
                    <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Login</a></li>
                    <li><a href="{{ route('register') }}" class="hover:text-white transition-colors">Register</a></li>
                @endauth
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 text-center text-xs text-slate-500 py-4">
        © {{ date('Y') }} Shoparoo. Shop more, save more.
    </div>
</footer>

<!-- Toast -->
<div
    x-data="toast"
    x-show="visible"
    x-transition
    x-cloak
    @notify.window="show($event.detail.message, $event.detail.type || 'success')"
    class="fixed w-[min(400px,calc(100%-2rem))] left-1/2 -translate-x-1/2 top-20 py-3 px-4 pb-5 text-white rounded-lg shadow-lift z-[200]"
    :class="type === 'success' ? 'bg-emerald-600' : 'bg-red-500'"
>
    <div class="font-semibold" x-text="message"></div>
    <button
        @click="close"
        class="absolute flex items-center justify-center right-2 top-2 w-[30px] h-[30px] rounded-full hover:bg-black/10 transition-colors"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
    </button>
    <div class="absolute left-0 bottom-0 right-0 h-[5px] bg-black/10 rounded-b-lg overflow-hidden">
        <div class="h-full bg-white/40" :style="{'width': `${percent}%`}"></div>
    </div>
</div>
</body>
</html>
