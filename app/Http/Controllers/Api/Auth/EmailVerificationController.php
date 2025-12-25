<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Get email verification status.
     */
    public function status(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'email' => $user->email,
                'verified' => $user->hasVerifiedEmail(),
                'verified_at' => $user->email_verified_at,
            ],
        ]);
    }

    /**
     * Send a new email verification notification.
     */
    public function send(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email is already verified.',
                'data' => [
                    'verified' => true,
                    'verified_at' => $user->email_verified_at,
                ],
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Verification link has been sent to your email.',
        ]);
    }

    /**
     * Verify email with hash (for mobile/API apps using deep links).
     */
    public function verify(Request $request, string $id, string $hash): JsonResponse
    {
        $user = $request->user();

        // Verify the user ID matches
        if ($user->getKey() !== $id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link.',
            ], 403);
        }

        // Verify the hash
        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification link.',
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => true,
                'message' => 'Email is already verified.',
                'data' => [
                    'verified' => true,
                    'verified_at' => $user->email_verified_at,
                ],
            ]);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->json([
            'success' => true,
            'message' => 'Email has been verified successfully.',
            'data' => [
                'verified' => true,
                'verified_at' => $user->email_verified_at,
            ],
        ]);
    }
}
