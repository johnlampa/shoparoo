<?php

namespace App\Http\Controllers\Auth;

use App\Enums\CustomerStatus;
use App\Helpers\Cart;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = null;

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $customer = new Customer();
            $names = explode(' ', $user->name, 2);
            $customer->user_id = $user->id;
            $customer->first_name = $names[0];
            $customer->last_name = $names[1] ?? '';
            $customer->status = CustomerStatus::Active->value;
            $customer->save();

            Auth::login($user);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('registration_failed', [
                'email' => $request->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()->withInput()->with('error', 'Unable to register right now.');
        }

        DB::commit();

        Cart::moveCartItemsIntoDb();

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            Log::channel('stderr')->error('registration_verification_email_failed', [
                'user_id' => $user?->id,
                'email' => $user?->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('verification.notice')
                ->with('warning', 'Account created, but verification email could not be sent right now. You can resend it below.');
        }

        return redirect()->route('verification.notice');
    }
}
