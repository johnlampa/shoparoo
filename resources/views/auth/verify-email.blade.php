<x-app-layout>
    <div class="w-[400px] mx-auto py-32">

        <div class="mb-4 text-sm text-gray-600">
            {{ __('Thanks for signing up! Before getting started, please verify your email address. Check your inbox (and spam folder) for a message from us. If you didn\'t receive it, you can resend below.') }}
        </div>
        <div class="mb-4 text-sm text-gray-500">
            Signed in as <span class="font-medium text-gray-700">{{ auth()->user()->email }}</span>
        </div>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ __('A new verification link has been sent to the email address you provided during registration.') }}
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-4 font-medium text-sm text-amber-600">
                {{ session('warning') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <div class="mt-4 flex items-center justify-between">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf

                <div>
                    <x-button>
                        {{ __('Resend Verification Email') }}
                    </x-button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </div>
</x-app-layout>
