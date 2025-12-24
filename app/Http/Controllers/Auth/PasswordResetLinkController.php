<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/ForgotPassword', [
            'status' => session('status'),
            'token' => session('token'),
            'verified' => session('verified'),
            'reset_token' => session('reset_token'),
            'email' => session('email'),
        ]);
    }

    /**
     * Handle an incoming password reset link request - Send OTP.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
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
            'created_at' => Carbon::now()
        ]);

        // Send OTP via email
        try {
            Mail::to($request->email)->send(new PasswordResetOtpMail($otp, $user->name ?? $user->username ?? 'User', 10));

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

        return back()->with([
            'status' => 'We have emailed your password reset OTP!',
            'token' => $token,
        ]);
    }

    /**
     * Verify OTP for password reset.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
            'token' => 'required|string'
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
                'token' => Hash::make($resetToken)
            ]);

        return back()->with([
            'verified' => true,
            'reset_token' => $resetToken,
            'email' => $request->email,
        ]);
    }

    /**
     * Resend OTP for password reset.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        // Find the reset record by checking if any token matches
        $resetRecord = DB::table('password_reset_tokens')
            ->where('is_verified', false)
            ->get()
            ->first(function ($record) use ($request) {
                return Hash::check($request->token, $record->token);
            });

        if (!$resetRecord) {
            throw ValidationException::withMessages([
                'token' => ['Invalid or expired session. Please start over.'],
            ]);
        }

        // Generate new OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $newToken = Str::random(60);

        // Update with new OTP
        DB::table('password_reset_tokens')
            ->where('email', $resetRecord->email)
            ->update([
                'otp' => $otp,
                'token' => Hash::make($newToken),
                'otp_expires_at' => Carbon::now()->addMinutes(10)
            ]);

        // Send OTP via email
        try {
            $user = User::where('email', $resetRecord->email)->first();
            Mail::to($resetRecord->email)->send(new PasswordResetOtpMail($otp, $user->name ?? $user->username ?? 'User', 10));

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP (resend) sent to {$resetRecord->email}: {$otp}");
            }
        } catch (\Exception $e) {
            \Log::error("Failed to send password reset OTP (resend) to {$resetRecord->email}: " . $e->getMessage());

            // Only log OTP in local/development environment for testing
            if (app()->environment('local', 'testing')) {
                \Log::debug("Password reset OTP (resend) for {$resetRecord->email}: {$otp}");
            }
        }

        return back()->with([
            'status' => 'New OTP sent to your email!',
            'token' => $newToken,
        ]);
    }
}
