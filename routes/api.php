<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// API Version 1
Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Authentication Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        // Login
        Route::post('/login', [LoginController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('api.login');

        // Register
        Route::post('/register', [RegisterController::class, 'store'])
            ->middleware('throttle:5,1')
            ->name('api.register');

        // Password Reset
        Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])
            ->middleware('throttle:5,1')
            ->name('api.password.email');

        Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])
            ->middleware('throttle:10,1')
            ->name('api.password.verify-otp');

        Route::post('/resend-otp', [PasswordResetController::class, 'resendOtp'])
            ->middleware('throttle:3,1')
            ->name('api.password.resend-otp');

        Route::post('/reset-password', [PasswordResetController::class, 'resetPassword'])
            ->middleware('throttle:5,1')
            ->name('api.password.reset');
    });

    /*
    |--------------------------------------------------------------------------
    | Protected Routes (Requires Authentication)
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth:sanctum')->group(function () {

        // Auth Routes
        Route::prefix('auth')->group(function () {
            // Logout
            Route::post('/logout', [LoginController::class, 'destroy'])
                ->name('api.logout');

            // Logout from all devices
            Route::post('/logout-all', [LoginController::class, 'destroyAll'])
                ->name('api.logout.all');
        });

        // User Profile Routes
        Route::prefix('user')->group(function () {
            // Get current user
            Route::get('/', [UserController::class, 'show'])
                ->name('api.user.show');

            // Update profile
            Route::put('/', [UserController::class, 'update'])
                ->name('api.user.update');

            Route::patch('/', [UserController::class, 'update']);

            // Update password
            Route::put('/password', [UserController::class, 'updatePassword'])
                ->name('api.user.password');

            // Delete account
            Route::delete('/', [UserController::class, 'destroy'])
                ->name('api.user.destroy');
        });

        // Email Verification Routes
        Route::prefix('email')->group(function () {
            // Get verification status
            Route::get('/verify', [EmailVerificationController::class, 'status'])
                ->name('api.verification.status');

            // Send verification email
            Route::post('/verification-notification', [EmailVerificationController::class, 'send'])
                ->middleware('throttle:6,1')
                ->name('api.verification.send');

            // Verify email with hash
            Route::post('/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('api.verification.verify');
        });

        /*
        |--------------------------------------------------------------------------
        | Snippet Routes (To be implemented)
        |--------------------------------------------------------------------------
        */
        // Route::apiResource('snippets', SnippetController::class);
        // Route::post('/snippets/{snippet}/favorite', [SnippetController::class, 'toggleFavorite']);
        // Route::post('/snippets/{snippet}/fork', [SnippetController::class, 'fork']);

        /*
        |--------------------------------------------------------------------------
        | Team Routes (To be implemented)
        |--------------------------------------------------------------------------
        */
        // Route::apiResource('teams', TeamController::class);
        // Route::post('/teams/{team}/invite', [TeamController::class, 'invite']);
        // Route::post('/teams/{team}/leave', [TeamController::class, 'leave']);

    });
});

/*
|--------------------------------------------------------------------------
| Health Check
|--------------------------------------------------------------------------
*/
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is running.',
        'version' => 'v1',
        'timestamp' => now()->toIso8601String(),
    ]);
})->name('api.health');
