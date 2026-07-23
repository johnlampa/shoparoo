<x-app-layout>
    <form method="POST" action="{{ route('login') }}" class="section-shell w-full max-w-md mx-auto p-6 sm:p-8 my-8 sm:my-12">
        <h2 class="font-display text-2xl font-bold text-center text-ink-900 mb-2">
            Login to your account
        </h2>
        <p class="text-center text-slate-500 mb-6">
            or
            <a
                href="{{ route('register') }}"
                class="text-sm font-medium text-brand-600 hover:text-brand-700"
            >
                create new account
            </a>
        </p>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')"/>

        @csrf
        <div class="mb-4">
            <x-input type="email" name="email" placeholder="Your email address" :value="old('email')"/>
        </div>
        <div class="mb-4">
            <x-input type="password" name="password" placeholder="Your password" :value="old('password')" />
        </div>
        <div class="flex justify-between items-center mb-5">
            <div class="flex items-center">
                <input
                    id="loginRememberMe"
                    type="checkbox"
                    name="remember"
                    class="mr-3 rounded border-gray-300 text-brand-500 focus:ring-brand-500"
                />
                <label for="loginRememberMe">Remember Me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                    Forgot Password?
                </a>
            @endif
        </div>
        <button type="submit" class="btn-primary w-full py-3">
            Login
        </button>
    </form>
</x-app-layout>
