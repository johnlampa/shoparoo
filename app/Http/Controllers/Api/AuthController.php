<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'=> ['required', 'email'],
            'password' => 'required',
            'remember' => 'boolean'
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response([
                'message' => 'Email or password is incorrect'
            ], 422);
        }

        try {
            if (!$user->is_admin) {
                return response([
                    'message' => 'You don\'t have permission to authenticate as admin'
                ], 403);
            }

            if (!$user->email_verified_at) {
                return response([
                    'message' => 'Your email address is not verified'
                ], 403);
            }

            $token = $user->createToken('main')->plainTextToken;

            return response([
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at?->format('Y-m-d H:i:s'),
                ],
                'token' => $token
            ]);
        } catch (\Throwable $e) {
            Log::error('Login failed after authentication', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response([
                'message' => 'Login failed after authentication',
            ], 500);
        }

    }

    public function logout()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->currentAccessToken()->delete();

        return response('', 204);
    }

    public function getUser(Request $request)
    {
        return new UserResource($request->user());
    }
}
