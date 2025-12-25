<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    /**
     * Get authenticated user's profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'bio' => $user->bio,
                'avatar_url' => $user->avatar_url,
                'website_url' => $user->website_url,
                'github_url' => $user->github_url,
                'twitter_url' => $user->twitter_url,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update authenticated user's profile.
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'username' => [
                'sometimes',
                'string',
                'min:3',
                'max:30',
                'regex:/^[a-zA-Z0-9_-]+$/',
                'unique:users,username,' . $user->id,
            ],
            'full_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'website_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
        ], [
            'username.regex' => 'Username can only contain letters, numbers, underscores, and hyphens.',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'data' => [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'full_name' => $user->full_name,
                'bio' => $user->bio,
                'avatar_url' => $user->avatar_url,
                'website_url' => $user->website_url,
                'github_url' => $user->github_url,
                'twitter_url' => $user->twitter_url,
                'is_admin' => $user->is_admin,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
            ],
        ]);
    }

    /**
     * Update authenticated user's password.
     *
     * @throws ValidationException
     */
    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();

        // Verify current password
        if (!Hash::check($request->current_password, $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => ['The current password is incorrect.'],
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password updated successfully.',
        ]);
    }

    /**
     * Delete authenticated user's account.
     *
     * @throws ValidationException
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();

        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => ['The password is incorrect.'],
            ]);
        }

        // Revoke all tokens
        $user->tokens()->delete();

        // Soft delete user
        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Account deleted successfully.',
        ]);
    }
}
