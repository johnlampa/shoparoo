<x-app-layout>
    <form action="{{ route('password.email') }}" method="post" class="section-shell w-full max-w-md mx-auto p-6 sm:p-8 my-8 sm:my-12">
        @csrf
        <h2 class="font-display text-2xl font-bold text-center text-ink-900 mb-2">
            Reset your password
        </h2>

        <x-auth-session-status class="mb-4" :status="session('status')"/>

        <p class="text-center text-slate-500 mb-6">
            or
            <a
                href="{{ route('login') }}"
                class="font-medium text-brand-600 hover:text-brand-700"
            >
                login with existing account
            </a>
        </p>

        <div class="mb-3">
            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                     autofocus placeholder="Enter your Email Address"/>
        </div>
        <button type="submit" class="btn-primary w-full py-3">
            Email Password Reset Link
        </button>
    </form>
</x-app-layout>
