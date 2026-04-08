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
        $stage = 'validate_request';

        $credentials = $request->validate([
            'email'=> ['required', 'email'],
            'password' => 'required',
            'remember' => 'boolean'
        ]);

        $stage = 'find_user';
        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return response([
                'message' => 'Email or password is incorrect'
            ], 422);
        }

        try {
            $stage = 'check_admin';
            Log::channel('stderr')->info('Login attempt for user', ['user_id' => $user->id, 'email' => $user->email]);

            if (!$user->is_admin) {
                Log::channel('stderr')->warning('Non-admin user attempted login', ['user_id' => $user->id]);
                return response([
                    'message' => 'You don\'t have permission to authenticate as admin'
                ], 403);
            }

            $stage = 'check_email_verified';
            if (!$user->email_verified_at) {
                Log::channel('stderr')->warning('Unverified email user attempted login', ['user_id' => $user->id]);
                return response([
                    'message' => 'Your email address is not verified'
                ], 403);
            }

            $stage = 'create_token';
            Log::channel('stderr')->info('Creating token for user', ['user_id' => $user->id]);
            $token = $user->createToken('main')->plainTextToken;
            Log::channel('stderr')->info('Token created successfully', ['user_id' => $user->id]);

            $stage = 'build_response';
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
            Log::channel('stderr')->error('Login failed after authentication', [
                'user_id' => $user->id,
                'email' => $user->email,
                'stage' => $stage,
                'exception' => $e::class,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response([
                'message' => 'Login failed after authentication',
                'stage' => $stage,
                'error' => $e->getMessage(),
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
