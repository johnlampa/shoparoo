<x-app-layout>
    <form
        action="{{ route('register') }}"
        method="post"
        class="section-shell w-full max-w-md mx-auto p-6 sm:p-8 my-8 sm:my-12"
    >
        @csrf

        <h2 class="font-display text-2xl font-bold text-center text-ink-900 mb-2">Create an account</h2>
        <p class="text-center text-slate-500 mb-6">
            or
            <a
                href="{{ route('login') }}"
                class="text-sm font-medium text-brand-600 hover:text-brand-700"
            >
                login with existing account
            </a>
        </p>

        @if (session('error'))
            <div class="py-2 px-3 bg-red-500 text-white mb-2 rounded">
                {{ session('error') }}
            </div>
        @endif

        <x-auth-session-status class="mb-4" :status="session('status')"/>

        <div class="mb-4">
            <x-input placeholder="Your name" type="text" name="name" :value="old('name')" />
        </div>
        <div class="mb-4">
            <x-input placeholder="Your Email" type="email" name="email" :value="old('email')" />
        </div>
        <div class="mb-4">
            <x-input placeholder="Password" type="password" name="password"/>
        </div>
        <div class="mb-4">
            <x-input placeholder="Repeat Password" type="password" name="password_confirmation"/>
        </div>

        <button type="submit" class="btn-primary w-full py-3">
            Signup
        </button>
    </form>
</x-app-layout>
