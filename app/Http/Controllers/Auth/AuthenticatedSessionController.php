<?php

namespace App\Http\Controllers\Auth;

use App\Helpers\Cart;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        Cart::moveCartItemsIntoDb();

        if (!$request->user()->hasVerifiedEmail()) {
            try {
                $request->user()->sendEmailVerificationNotification();

                return redirect()
                    ->route('verification.notice')
                    ->with('status', 'verification-link-sent');
            } catch (\Throwable $e) {
                Log::channel('stderr')->error('login_verification_email_failed', [
                    'user_id' => $request->user()->id,
                    'email' => $request->user()->email,
                    'error' => $e->getMessage(),
                ]);

                return redirect()
                    ->route('verification.notice')
                    ->with('error', 'You need to verify your email before continuing. We could not send the verification email right now — check that BREVO_API_KEY is set correctly on the server, then use Resend below.');
            }
        }

        return redirect()->intended(route('home'));
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
