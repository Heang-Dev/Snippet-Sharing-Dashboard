<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\SnippetController;
use App\Http\Controllers\Api\V1\TagController;
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
    | Languages Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('languages')->group(function () {
        Route::get('/', [LanguageController::class, 'index'])
            ->name('api.languages.index');

        Route::get('/popular', [LanguageController::class, 'popular'])
            ->name('api.languages.popular');

        Route::get('/{slug}', [LanguageController::class, 'show'])
            ->name('api.languages.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Categories Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])
            ->name('api.categories.index');

        Route::get('/tree', [CategoryController::class, 'tree'])
            ->name('api.categories.tree');

        Route::get('/{slug}', [CategoryController::class, 'show'])
            ->name('api.categories.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Tags Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index'])
            ->name('api.tags.index');

        Route::get('/popular', [TagController::class, 'popular'])
            ->name('api.tags.popular');

        Route::get('/search', [TagController::class, 'search'])
            ->name('api.tags.search');

        Route::get('/{slug}', [TagController::class, 'show'])
            ->name('api.tags.show');
    });

    /*
    |--------------------------------------------------------------------------
    | Public Snippet Routes (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('snippets')->group(function () {
        // Browse public snippets
        Route::get('/public', [SnippetController::class, 'publicIndex'])
            ->name('api.snippets.public');

        // Get trending snippets
        Route::get('/trending', [SnippetController::class, 'trending'])
            ->name('api.snippets.trending');

        // Get featured snippets
        Route::get('/featured', [SnippetController::class, 'featured'])
            ->name('api.snippets.featured');

        // View single snippet (public or accessible)
        Route::get('/{slug}', [SnippetController::class, 'show'])
            ->name('api.snippets.show');
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
        | Snippet Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('snippets')->group(function () {
            // List user's snippets
            Route::get('/', [SnippetController::class, 'index'])
                ->name('api.snippets.index');

            // Get user's favorite snippets
            Route::get('/favorites', [SnippetController::class, 'favorites'])
                ->name('api.snippets.favorites');

            // Create snippet
            Route::post('/', [SnippetController::class, 'store'])
                ->name('api.snippets.store');

            // Update snippet
            Route::put('/{id}', [SnippetController::class, 'update'])
                ->name('api.snippets.update');

            Route::patch('/{id}', [SnippetController::class, 'update']);

            // Delete snippet
            Route::delete('/{id}', [SnippetController::class, 'destroy'])
                ->name('api.snippets.destroy');

            // Toggle favorite
            Route::post('/{id}/favorite', [SnippetController::class, 'toggleFavorite'])
                ->name('api.snippets.favorite');

            // Fork snippet
            Route::post('/{id}/fork', [SnippetController::class, 'fork'])
                ->name('api.snippets.fork');

            // Get forks of a snippet
            Route::get('/{id}/forks', [SnippetController::class, 'forks'])
                ->name('api.snippets.forks');
        });

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
