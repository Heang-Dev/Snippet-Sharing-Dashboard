<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Handle an API login request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
        ]);

        // Check if login is email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login' => ['The provided credentials do not match our records.'],
            ]);
        }

        // Check if user is active
        if (!$user->is_active) {
            throw ValidationException::withMessages([
                'login' => ['Your account has been deactivated.'],
            ]);
        }

        // Update last login timestamp
        $user->update(['last_login_at' => now()]);

        // Create token with device name
        $deviceName = $request->device_name ?? 'api-token';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'username' => $user->username,
                    'email' => $user->email,
                    'full_name' => $user->full_name,
                    'avatar_url' => $user->avatar_url,
                    'is_admin' => $user->is_admin,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    /**
     * Handle an API logout request.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Revoke current token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully.',
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     */
    public function destroyAll(Request $request): JsonResponse
    {
        // Revoke all tokens
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out from all devices successfully.',
        ]);
    }
}
