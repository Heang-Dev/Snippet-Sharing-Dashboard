<?php

use App\Http\Controllers\Api\Auth\EmailVerificationController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Auth\UserController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LanguageController;
use App\Http\Controllers\Api\V1\SnippetController;
use App\Http\Controllers\Api\V1\CollectionController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\TagController;
use App\Http\Controllers\Api\V1\TeamController;
use App\Http\Controllers\Api\V1\FollowController;
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
    | Public Collections Routes (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('collections')->group(function () {
        Route::get('/public', [CollectionController::class, 'publicIndex'])
            ->name('api.collections.public');

        Route::get('/{id}', [CollectionController::class, 'show'])
            ->name('api.collections.show');

        Route::get('/{id}/snippets', [CollectionController::class, 'snippets'])
            ->name('api.collections.snippets');
    });

    /*
    |--------------------------------------------------------------------------
    | Public Comments Routes (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('comments')->group(function () {
        // Get comment by ID
        Route::get('/{id}', [CommentController::class, 'show'])
            ->name('api.comments.show');

        // Get replies to a comment
        Route::get('/{id}/replies', [CommentController::class, 'replies'])
            ->name('api.comments.replies');
    });

    // Get comments for a snippet (public)
    Route::get('/snippets/{snippetId}/comments', [CommentController::class, 'index'])
        ->name('api.snippets.comments');

    /*
    |--------------------------------------------------------------------------
    | Search Routes (Public)
    |--------------------------------------------------------------------------
    */
    Route::prefix('search')->group(function () {
        // Global search
        Route::get('/', [SearchController::class, 'index'])
            ->name('api.search');

        // Search snippets with advanced filters
        Route::get('/snippets', [SearchController::class, 'snippets'])
            ->name('api.search.snippets');

        // Search users
        Route::get('/users', [SearchController::class, 'users'])
            ->name('api.search.users');

        // Autocomplete/suggestions
        Route::get('/autocomplete', [SearchController::class, 'autocomplete'])
            ->name('api.search.autocomplete');
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
        | Collection Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('collections')->group(function () {
            // List user's collections
            Route::get('/', [CollectionController::class, 'index'])
                ->name('api.collections.index');

            // Create collection
            Route::post('/', [CollectionController::class, 'store'])
                ->name('api.collections.store');

            // Update collection
            Route::put('/{id}', [CollectionController::class, 'update'])
                ->name('api.collections.update');

            Route::patch('/{id}', [CollectionController::class, 'update']);

            // Delete collection
            Route::delete('/{id}', [CollectionController::class, 'destroy'])
                ->name('api.collections.destroy');

            // Add snippet to collection
            Route::post('/{id}/snippets', [CollectionController::class, 'addSnippet'])
                ->name('api.collections.addSnippet');

            // Remove snippet from collection
            Route::delete('/{id}/snippets/{snippetId}', [CollectionController::class, 'removeSnippet'])
                ->name('api.collections.removeSnippet');

            // Reorder snippets in collection
            Route::put('/{id}/reorder', [CollectionController::class, 'reorderSnippets'])
                ->name('api.collections.reorder');
        });

        /*
        |--------------------------------------------------------------------------
        | Comment Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('comments')->group(function () {
            // Get user's comments
            Route::get('/me', [CommentController::class, 'userComments'])
                ->name('api.comments.me');

            // Update comment
            Route::put('/{id}', [CommentController::class, 'update'])
                ->name('api.comments.update');

            Route::patch('/{id}', [CommentController::class, 'update']);

            // Delete comment
            Route::delete('/{id}', [CommentController::class, 'destroy'])
                ->name('api.comments.destroy');
        });

        // Create comment on snippet
        Route::post('/snippets/{snippetId}/comments', [CommentController::class, 'store'])
            ->name('api.snippets.comments.store');

        /*
        |--------------------------------------------------------------------------
        | Team Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('teams')->group(function () {
            // List user's teams
            Route::get('/', [TeamController::class, 'index'])
                ->name('api.teams.index');

            // Create team
            Route::post('/', [TeamController::class, 'store'])
                ->name('api.teams.store');

            // Get team details
            Route::get('/{id}', [TeamController::class, 'show'])
                ->name('api.teams.show');

            // Update team
            Route::put('/{id}', [TeamController::class, 'update'])
                ->name('api.teams.update');

            Route::patch('/{id}', [TeamController::class, 'update']);

            // Delete team
            Route::delete('/{id}', [TeamController::class, 'destroy'])
                ->name('api.teams.destroy');

            // Get team members
            Route::get('/{id}/members', [TeamController::class, 'members'])
                ->name('api.teams.members');

            // Update member role
            Route::put('/{id}/members/{memberId}', [TeamController::class, 'updateMemberRole'])
                ->name('api.teams.members.update');

            // Remove member
            Route::delete('/{id}/members/{memberId}', [TeamController::class, 'removeMember'])
                ->name('api.teams.members.remove');

            // Get team snippets
            Route::get('/{id}/snippets', [TeamController::class, 'snippets'])
                ->name('api.teams.snippets');

            // Invite member
            Route::post('/{id}/invite', [TeamController::class, 'invite'])
                ->name('api.teams.invite');

            // Get team invitations (owner/admin only)
            Route::get('/{id}/invitations', [TeamController::class, 'invitations'])
                ->name('api.teams.invitations');

            // Cancel invitation
            Route::delete('/{id}/invitations/{invitationId}', [TeamController::class, 'cancelInvitation'])
                ->name('api.teams.invitations.cancel');

            // Leave team
            Route::post('/{id}/leave', [TeamController::class, 'leave'])
                ->name('api.teams.leave');

            // Transfer ownership
            Route::post('/{id}/transfer', [TeamController::class, 'transferOwnership'])
                ->name('api.teams.transfer');
        });

        /*
        |--------------------------------------------------------------------------
        | Team Invitation Routes (User's invitations)
        |--------------------------------------------------------------------------
        */
        Route::prefix('invitations')->group(function () {
            // Get my pending invitations
            Route::get('/', [TeamController::class, 'myInvitations'])
                ->name('api.invitations.index');

            // Accept invitation
            Route::post('/{invitationId}/accept', [TeamController::class, 'acceptInvitation'])
                ->name('api.invitations.accept');

            // Decline invitation
            Route::post('/{invitationId}/decline', [TeamController::class, 'declineInvitation'])
                ->name('api.invitations.decline');
        });

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
