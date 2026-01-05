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
use App\Http\Controllers\Api\V1\NotificationController;
use App\Http\Controllers\Api\V1\ShareController;
use App\Http\Controllers\Api\V1\SnippetVersionController;
use App\Http\Controllers\Api\V1\ActivityFeedController;
use App\Http\Controllers\Api\V1\AdminController;
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
    | Public Share Access (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    Route::get('/shares/token/{token}', [ShareController::class, 'accessByToken'])
        ->name('api.shares.access-by-token');

    /*
    |--------------------------------------------------------------------------
    | Public Activity Feed (No Authentication Required)
    |--------------------------------------------------------------------------
    */
    Route::prefix('feed')->group(function () {
        // Public activity feed
        Route::get('/public', [ActivityFeedController::class, 'publicFeed'])
            ->name('api.feed.public');

        // Activity types reference
        Route::get('/types', [ActivityFeedController::class, 'types'])
            ->name('api.feed.types');

        // User activity (public profile)
        Route::get('/users/{userId}', [ActivityFeedController::class, 'userActivity'])
            ->name('api.feed.user');
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

        /*
        |--------------------------------------------------------------------------
        | Follow Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('follows')->group(function () {
            // Get my followers
            Route::get('/followers', [FollowController::class, 'myFollowers'])
                ->name('api.follows.my-followers');

            // Get users I'm following
            Route::get('/following', [FollowController::class, 'myFollowing'])
                ->name('api.follows.my-following');

            // Get follow suggestions
            Route::get('/suggestions', [FollowController::class, 'suggestions'])
                ->name('api.follows.suggestions');
        });

        // User-specific follow routes
        Route::prefix('users/{userId}')->group(function () {
            // Follow a user
            Route::post('/follow', [FollowController::class, 'follow'])
                ->name('api.users.follow');

            // Unfollow a user
            Route::delete('/follow', [FollowController::class, 'unfollow'])
                ->name('api.users.unfollow');

            // Toggle follow status
            Route::post('/follow/toggle', [FollowController::class, 'toggle'])
                ->name('api.users.follow.toggle');

            // Check follow status
            Route::get('/follow/check', [FollowController::class, 'check'])
                ->name('api.users.follow.check');

            // Get user's followers
            Route::get('/followers', [FollowController::class, 'followers'])
                ->name('api.users.followers');

            // Get users that user is following
            Route::get('/following', [FollowController::class, 'following'])
                ->name('api.users.following');

            // Get follow stats
            Route::get('/follow/stats', [FollowController::class, 'stats'])
                ->name('api.users.follow.stats');

            // Update notification settings for follow
            Route::put('/follow/notifications', [FollowController::class, 'updateNotificationSettings'])
                ->name('api.users.follow.notifications');

            // Get mutual followers
            Route::get('/mutual-followers', [FollowController::class, 'mutualFollowers'])
                ->name('api.users.mutual-followers');
        });

        /*
        |--------------------------------------------------------------------------
        | Notification Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('notifications')->group(function () {
            // Get notification types (public reference)
            Route::get('/types', [NotificationController::class, 'types'])
                ->name('api.notifications.types');

            // Get all notifications
            Route::get('/', [NotificationController::class, 'index'])
                ->name('api.notifications.index');

            // Get unread count
            Route::get('/unread-count', [NotificationController::class, 'unreadCount'])
                ->name('api.notifications.unread-count');

            // Get notification settings
            Route::get('/settings', [NotificationController::class, 'settings'])
                ->name('api.notifications.settings');

            // Update notification settings
            Route::put('/settings', [NotificationController::class, 'updateSettings'])
                ->name('api.notifications.settings.update');

            // Mark all as read
            Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])
                ->name('api.notifications.mark-all-read');

            // Mark multiple as read
            Route::post('/mark-read', [NotificationController::class, 'markMultipleAsRead'])
                ->name('api.notifications.mark-read');

            // Delete multiple notifications
            Route::delete('/batch', [NotificationController::class, 'destroyMultiple'])
                ->name('api.notifications.destroy-multiple');

            // Delete all read notifications
            Route::delete('/read', [NotificationController::class, 'destroyAllRead'])
                ->name('api.notifications.destroy-read');

            // Delete all notifications
            Route::delete('/all', [NotificationController::class, 'destroyAll'])
                ->name('api.notifications.destroy-all');

            // Get specific notification
            Route::get('/{id}', [NotificationController::class, 'show'])
                ->name('api.notifications.show');

            // Mark as read
            Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])
                ->name('api.notifications.read');

            // Mark as unread
            Route::post('/{id}/unread', [NotificationController::class, 'markAsUnread'])
                ->name('api.notifications.unread');

            // Delete notification
            Route::delete('/{id}', [NotificationController::class, 'destroy'])
                ->name('api.notifications.destroy');
        });

        /*
        |--------------------------------------------------------------------------
        | Share Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('shares')->group(function () {
            // Get snippets shared with me
            Route::get('/with-me', [ShareController::class, 'sharedWithMe'])
                ->name('api.shares.with-me');

            // Get snippets I've shared
            Route::get('/by-me', [ShareController::class, 'sharedByMe'])
                ->name('api.shares.by-me');

            // Get specific share
            Route::get('/{id}', [ShareController::class, 'show'])
                ->name('api.shares.show');

            // Update share
            Route::put('/{id}', [ShareController::class, 'update'])
                ->name('api.shares.update');

            Route::patch('/{id}', [ShareController::class, 'update']);

            // Delete share
            Route::delete('/{id}', [ShareController::class, 'destroy'])
                ->name('api.shares.destroy');

            // Regenerate share token
            Route::post('/{id}/regenerate-token', [ShareController::class, 'regenerateToken'])
                ->name('api.shares.regenerate-token');
        });

        // Snippet-specific share routes
        Route::prefix('snippets/{snippetId}/shares')->group(function () {
            // Get shares for a snippet
            Route::get('/', [ShareController::class, 'index'])
                ->name('api.snippets.shares.index');

            // Create share for a snippet
            Route::post('/', [ShareController::class, 'store'])
                ->name('api.snippets.shares.store');

            // Revoke all shares for a snippet
            Route::post('/revoke-all', [ShareController::class, 'revokeAll'])
                ->name('api.snippets.shares.revoke-all');
        });

        /*
        |--------------------------------------------------------------------------
        | Snippet Version Routes
        |--------------------------------------------------------------------------
        */
        Route::prefix('snippets/{snippetId}/versions')->group(function () {
            // Get all versions for a snippet
            Route::get('/', [SnippetVersionController::class, 'index'])
                ->name('api.snippets.versions.index');

            // Get version stats
            Route::get('/stats', [SnippetVersionController::class, 'stats'])
                ->name('api.snippets.versions.stats');

            // Get latest version
            Route::get('/latest', [SnippetVersionController::class, 'latest'])
                ->name('api.snippets.versions.latest');

            // Compare two versions
            Route::get('/compare', [SnippetVersionController::class, 'compare'])
                ->name('api.snippets.versions.compare');

            // Get version by number
            Route::get('/number/{versionNumber}', [SnippetVersionController::class, 'showByNumber'])
                ->name('api.snippets.versions.show-by-number')
                ->where('versionNumber', '[0-9]+');

            // Get specific version by ID
            Route::get('/{versionId}', [SnippetVersionController::class, 'show'])
                ->name('api.snippets.versions.show');

            // Restore a version
            Route::post('/{versionId}/restore', [SnippetVersionController::class, 'restore'])
                ->name('api.snippets.versions.restore');
        });

        /*
        |--------------------------------------------------------------------------
        | Activity Feed Routes (Authenticated)
        |--------------------------------------------------------------------------
        */
        Route::prefix('feed')->group(function () {
            // Personalized activity feed
            Route::get('/', [ActivityFeedController::class, 'index'])
                ->name('api.feed.index');

            // My activity
            Route::get('/me', [ActivityFeedController::class, 'myActivity'])
                ->name('api.feed.me');

            // Activity statistics
            Route::get('/stats', [ActivityFeedController::class, 'stats'])
                ->name('api.feed.stats');
        });

        /*
        |--------------------------------------------------------------------------
        | Admin Routes (Requires Admin Privileges)
        |--------------------------------------------------------------------------
        */
        Route::prefix('admin')->group(function () {
            // Dashboard overview
            Route::get('/dashboard', [AdminController::class, 'dashboard'])
                ->name('api.admin.dashboard');

            // Analytics
            Route::get('/analytics', [AdminController::class, 'analytics'])
                ->name('api.admin.analytics');

            // Audit logs
            Route::get('/audit-logs', [AdminController::class, 'auditLogs'])
                ->name('api.admin.audit-logs');

            // User management
            Route::prefix('users')->group(function () {
                Route::get('/', [AdminController::class, 'users'])
                    ->name('api.admin.users.index');

                Route::get('/{id}', [AdminController::class, 'showUser'])
                    ->name('api.admin.users.show');

                Route::put('/{id}', [AdminController::class, 'updateUser'])
                    ->name('api.admin.users.update');

                Route::patch('/{id}', [AdminController::class, 'updateUser']);

                Route::delete('/{id}', [AdminController::class, 'deleteUser'])
                    ->name('api.admin.users.destroy');
            });

            // Snippet management
            Route::prefix('snippets')->group(function () {
                Route::get('/', [AdminController::class, 'snippets'])
                    ->name('api.admin.snippets.index');

                Route::put('/{id}', [AdminController::class, 'updateSnippet'])
                    ->name('api.admin.snippets.update');

                Route::patch('/{id}', [AdminController::class, 'updateSnippet']);

                Route::delete('/{id}', [AdminController::class, 'deleteSnippet'])
                    ->name('api.admin.snippets.destroy');
            });

            // Comment management
            Route::delete('/comments/{id}', [AdminController::class, 'deleteComment'])
                ->name('api.admin.comments.destroy');

            // Language management
            Route::prefix('languages')->group(function () {
                Route::post('/', [AdminController::class, 'createLanguage'])
                    ->name('api.admin.languages.store');

                Route::put('/{id}', [AdminController::class, 'updateLanguage'])
                    ->name('api.admin.languages.update');

                Route::patch('/{id}', [AdminController::class, 'updateLanguage']);

                Route::delete('/{id}', [AdminController::class, 'deleteLanguage'])
                    ->name('api.admin.languages.destroy');
            });

            // Category management
            Route::prefix('categories')->group(function () {
                Route::post('/', [AdminController::class, 'createCategory'])
                    ->name('api.admin.categories.store');

                Route::put('/{id}', [AdminController::class, 'updateCategory'])
                    ->name('api.admin.categories.update');

                Route::patch('/{id}', [AdminController::class, 'updateCategory']);

                Route::delete('/{id}', [AdminController::class, 'deleteCategory'])
                    ->name('api.admin.categories.destroy');
            });
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
