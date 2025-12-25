<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Handle an API registration request.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'username' => [
                'required',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_-]+$/',
                'unique:users',
            ],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:users',
            ],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'device_name' => ['nullable', 'string', 'max:255'],
        ], [
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
        ]);

        $user = User::create([
            'username' => $request->username,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        event(new Registered($user));

        // Create token for the new user
        $deviceName = $request->device_name ?? 'api-token';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registration successful. Please verify your email.',
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
        ], 201);
    }
}
