<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * Send password reset OTP to email.
     *
     * @throws ValidationException
     */
    public function sendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Check if user exists
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['We could not find a user with that email address.'],
            ]);
        }

        // Generate 6-digit OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $token = Str::random(60);

        // Delete old password reset requests for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Store OTP in password_reset_tokens table
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => Hash::make($token),
            'otp' => $otp,
            'otp_expires_at' => Carbon::now()->addMinutes(10),
            'is_verified' => false,
            'created_at' => Carbon::now(),
        ]);

        // Send OTP via email
        try {
            Mail::to($request->email)->send(
                new PasswordResetOtpMail($otp, $user->full_name ?? $user->username ?? 'User', 10)
            );

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP sent to {$request->email}: {$otp}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send password reset OTP to {$request->email}: " . $e->getMessage());

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP for {$request->email}: {$otp}");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset OTP has been sent to your email.',
            'data' => [
                'token' => $token,
                'expires_in' => 600, // 10 minutes in seconds
            ],
        ]);
    }

    /**
     * Verify OTP for password reset.
     *
     * @throws ValidationException
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'token' => ['required', 'string'],
        ]);

        // Find password reset record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid or expired reset request. Please request a new code.'],
            ]);
        }

        // Check if OTP is expired
        if (Carbon::parse($resetRecord->otp_expires_at)->isPast()) {
            throw ValidationException::withMessages([
                'otp' => ['OTP has expired. Please request a new code.'],
            ]);
        }

        // Verify OTP
        if ($resetRecord->otp !== $request->otp) {
            throw ValidationException::withMessages([
                'otp' => ['Invalid OTP code. Please try again.'],
            ]);
        }

        // Mark as verified and generate reset token
        $resetToken = Str::random(60);

        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'is_verified' => true,
                'token' => Hash::make($resetToken),
            ]);

        return response()->json([
            'success' => true,
            'message' => 'OTP verified successfully. You can now reset your password.',
            'data' => [
                'reset_token' => $resetToken,
                'email' => $request->email,
            ],
        ]);
    }

    /**
     * Resend OTP for password reset.
     *
     * @throws ValidationException
     */
    public function resendOtp(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
        ]);

        // Find reset record
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('is_verified', false)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired session. Please start over.'],
            ]);
        }

        // Verify token
        if (!Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Invalid token. Please start over.'],
            ]);
        }

        // Generate new OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $newToken = Str::random(60);

        // Update with new OTP
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->update([
                'otp' => $otp,
                'token' => Hash::make($newToken),
                'otp_expires_at' => Carbon::now()->addMinutes(10),
            ]);

        // Send OTP via email
        try {
            $user = User::where('email', $request->email)->first();
            Mail::to($request->email)->send(
                new PasswordResetOtpMail($otp, $user->full_name ?? $user->username ?? 'User', 10)
            );

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP (resend) sent to {$request->email}: {$otp}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send password reset OTP (resend) to {$request->email}: " . $e->getMessage());

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP (resend) for {$request->email}: {$otp}");
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'New OTP has been sent to your email.',
            'data' => [
                'token' => $newToken,
                'expires_in' => 600, // 10 minutes in seconds
            ],
        ]);
    }

    /**
     * Reset password with verified token.
     *
     * @throws ValidationException
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Find reset record by verifying token
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('is_verified', true)
            ->first();

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'email' => ['Please verify your OTP first.'],
            ]);
        }

        // Verify the reset token
        if (!Hash::check($request->token, $resetRecord->token)) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired reset token.'],
            ]);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['User not found.'],
            ]);
        }

        // Update password
        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),
        ])->save();

        event(new PasswordReset($user));

        // Delete the password reset record
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password has been reset successfully. You can now login with your new password.',
        ]);
    }
}
