<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Comment;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityFeedController extends Controller
{
    /**
     * Activity types for filtering
     */
    const ACTIVITY_TYPES = [
        'snippet_created',
        'snippet_updated',
        'snippet_forked',
        'snippet_favorited',
        'comment_added',
        'follow',
        'collection_created',
        'team_created',
        'team_joined',
    ];

    /**
     * Get personalized activity feed for authenticated user
     * Shows activities from users they follow and their own activities
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = Auth::user();
        $perPage = min($request->get('per_page', 20), 100);

        // Get IDs of users being followed
        $followingIds = $user->following()->pluck('users.id')->toArray();
        $followingIds[] = $user->id; // Include own activities

        // Build feed from multiple sources
        $activities = collect();

        // Get recent snippets from followed users
        $recentSnippets = Snippet::whereIn('user_id', $followingIds)
            ->where('visibility', 'public')
            ->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($snippet) {
                return [
                    'type' => 'snippet_created',
                    'user' => $snippet->user,
                    'resource_type' => 'snippet',
                    'resource_id' => $snippet->id,
                    'resource' => [
                        'id' => $snippet->id,
                        'title' => $snippet->title,
                        'slug' => $snippet->slug,
                        'description' => $snippet->description,
                        'language' => $snippet->language,
                    ],
                    'message' => "{$snippet->user->username} created a new snippet",
                    'created_at' => $snippet->created_at,
                ];
            });

        $activities = $activities->merge($recentSnippets);

        // Get recent comments on public snippets from followed users
        $recentComments = Comment::whereIn('user_id', $followingIds)
            ->whereHas('snippet', function ($q) {
                $q->where('visibility', 'public');
            })
            ->with([
                'user:id,username,full_name,avatar_url',
                'snippet:id,title,slug',
            ])
            ->whereNull('parent_id') // Only root comments
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment_added',
                    'user' => $comment->user,
                    'resource_type' => 'comment',
                    'resource_id' => $comment->id,
                    'resource' => [
                        'id' => $comment->id,
                        'content' => \Illuminate\Support\Str::limit($comment->content, 100),
                        'snippet' => $comment->snippet,
                    ],
                    'message' => "{$comment->user->username} commented on a snippet",
                    'created_at' => $comment->created_at,
                ];
            });

        $activities = $activities->merge($recentComments);

        // Get recent favorites from followed users (using audit logs if available)
        // For now, we'll use snippets that were recently favorited

        // Sort all activities by created_at
        $activities = $activities->sortByDesc('created_at')->values();

        // Filter by type if requested
        if ($request->has('type')) {
            $type = $request->get('type');
            if (in_array($type, self::ACTIVITY_TYPES)) {
                $activities = $activities->where('type', $type)->values();
            }
        }

        // Filter by types (multiple)
        if ($request->has('types')) {
            $types = explode(',', $request->get('types'));
            $validTypes = array_intersect($types, self::ACTIVITY_TYPES);
            if (!empty($validTypes)) {
                $activities = $activities->whereIn('type', $validTypes)->values();
            }
        }

        // Paginate manually
        $page = $request->get('page', 1);
        $total = $activities->count();
        $activities = $activities->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'message' => 'Activity feed retrieved successfully.',
            'data' => $activities,
            'meta' => [
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Get public activity feed (trending/popular activities)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publicFeed(Request $request): JsonResponse
    {
        $perPage = min($request->get('per_page', 20), 100);
        $activities = collect();

        // Get trending public snippets
        $trendingSnippets = Snippet::where('visibility', 'public')
            ->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug'])
            ->withCount(['favorites', 'comments', 'forks'])
            ->orderByRaw('(favorites_count * 3 + comments_count * 2 + forks_count) DESC')
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($snippet) {
                return [
                    'type' => 'snippet_trending',
                    'user' => $snippet->user,
                    'resource_type' => 'snippet',
                    'resource_id' => $snippet->id,
                    'resource' => [
                        'id' => $snippet->id,
                        'title' => $snippet->title,
                        'slug' => $snippet->slug,
                        'description' => $snippet->description,
                        'language' => $snippet->language,
                        'favorites_count' => $snippet->favorites_count,
                        'comments_count' => $snippet->comments_count,
                        'forks_count' => $snippet->forks_count,
                    ],
                    'message' => "Trending snippet by {$snippet->user->username}",
                    'created_at' => $snippet->created_at,
                ];
            });

        $activities = $activities->merge($trendingSnippets);

        // Get recent public snippets
        $recentSnippets = Snippet::where('visibility', 'public')
            ->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($snippet) {
                return [
                    'type' => 'snippet_created',
                    'user' => $snippet->user,
                    'resource_type' => 'snippet',
                    'resource_id' => $snippet->id,
                    'resource' => [
                        'id' => $snippet->id,
                        'title' => $snippet->title,
                        'slug' => $snippet->slug,
                        'description' => $snippet->description,
                        'language' => $snippet->language,
                    ],
                    'message' => "{$snippet->user->username} shared a snippet",
                    'created_at' => $snippet->created_at,
                ];
            });

        $activities = $activities->merge($recentSnippets);

        // Sort and deduplicate
        $activities = $activities->unique('resource_id')->sortByDesc('created_at')->values();

        // Paginate
        $page = $request->get('page', 1);
        $total = $activities->count();
        $activities = $activities->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'message' => 'Public activity feed retrieved successfully.',
            'data' => $activities,
            'meta' => [
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
            ],
        ]);
    }

    /**
     * Get activity for a specific user
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function userActivity(Request $request, string $userId): JsonResponse
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $perPage = min($request->get('per_page', 20), 100);
        $activities = collect();

        // Get user's public snippets
        $userSnippets = Snippet::where('user_id', $userId)
            ->where('visibility', 'public')
            ->with(['language:id,name,slug'])
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($snippet) use ($targetUser) {
                return [
                    'type' => 'snippet_created',
                    'user' => [
                        'id' => $targetUser->id,
                        'username' => $targetUser->username,
                        'full_name' => $targetUser->full_name,
                        'avatar_url' => $targetUser->avatar_url,
                    ],
                    'resource_type' => 'snippet',
                    'resource_id' => $snippet->id,
                    'resource' => [
                        'id' => $snippet->id,
                        'title' => $snippet->title,
                        'slug' => $snippet->slug,
                        'description' => $snippet->description,
                        'language' => $snippet->language,
                    ],
                    'message' => 'Created a new snippet',
                    'created_at' => $snippet->created_at,
                ];
            });

        $activities = $activities->merge($userSnippets);

        // Get user's comments on public snippets
        $userComments = Comment::where('user_id', $userId)
            ->whereHas('snippet', function ($q) {
                $q->where('visibility', 'public');
            })
            ->with(['snippet:id,title,slug'])
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->limit(30)
            ->get()
            ->map(function ($comment) use ($targetUser) {
                return [
                    'type' => 'comment_added',
                    'user' => [
                        'id' => $targetUser->id,
                        'username' => $targetUser->username,
                        'full_name' => $targetUser->full_name,
                        'avatar_url' => $targetUser->avatar_url,
                    ],
                    'resource_type' => 'comment',
                    'resource_id' => $comment->id,
                    'resource' => [
                        'id' => $comment->id,
                        'content' => \Illuminate\Support\Str::limit($comment->content, 100),
                        'snippet' => $comment->snippet,
                    ],
                    'message' => 'Commented on a snippet',
                    'created_at' => $comment->created_at,
                ];
            });

        $activities = $activities->merge($userComments);

        // Sort by date
        $activities = $activities->sortByDesc('created_at')->values();

        // Paginate
        $page = $request->get('page', 1);
        $total = $activities->count();
        $activities = $activities->forPage($page, $perPage)->values();

        return response()->json([
            'success' => true,
            'message' => 'User activity retrieved successfully.',
            'data' => $activities,
            'meta' => [
                'current_page' => (int) $page,
                'last_page' => (int) ceil($total / $perPage),
                'per_page' => $perPage,
                'total' => $total,
                'user' => [
                    'id' => $targetUser->id,
                    'username' => $targetUser->username,
                    'full_name' => $targetUser->full_name,
                    'avatar_url' => $targetUser->avatar_url,
                ],
            ],
        ]);
    }

    /**
     * Get my activity (authenticated user's own activities)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myActivity(Request $request): JsonResponse
    {
        return $this->userActivity($request, Auth::id());
    }

    /**
     * Get activity types
     *
     * @return JsonResponse
     */
    public function types(): JsonResponse
    {
        $types = collect(self::ACTIVITY_TYPES)->map(function ($type) {
            return [
                'type' => $type,
                'description' => $this->getTypeDescription($type),
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Activity types retrieved successfully.',
            'data' => $types,
        ]);
    }

    /**
     * Get activity statistics for authenticated user
     *
     * @return JsonResponse
     */
    public function stats(): JsonResponse
    {
        $user = Auth::user();

        $snippetsCreated = Snippet::where('user_id', $user->id)->count();
        $snippetsThisWeek = Snippet::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $commentsCount = Comment::where('user_id', $user->id)->count();
        $commentsThisWeek = Comment::where('user_id', $user->id)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        $favoritesReceived = DB::table('favorites')
            ->join('snippets', 'snippets.id', '=', 'favorites.snippet_id')
            ->where('snippets.user_id', $user->id)
            ->count();

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        return response()->json([
            'success' => true,
            'message' => 'Activity statistics retrieved successfully.',
            'data' => [
                'snippets' => [
                    'total' => $snippetsCreated,
                    'this_week' => $snippetsThisWeek,
                ],
                'comments' => [
                    'total' => $commentsCount,
                    'this_week' => $commentsThisWeek,
                ],
                'favorites_received' => $favoritesReceived,
                'followers' => $followersCount,
                'following' => $followingCount,
            ],
        ]);
    }

    /**
     * Get description for activity type
     *
     * @param string $type
     * @return string
     */
    private function getTypeDescription(string $type): string
    {
        $descriptions = [
            'snippet_created' => 'New snippet created',
            'snippet_updated' => 'Snippet updated',
            'snippet_forked' => 'Snippet forked',
            'snippet_favorited' => 'Snippet favorited',
            'comment_added' => 'Comment added',
            'follow' => 'New follow',
            'collection_created' => 'Collection created',
            'team_created' => 'Team created',
            'team_joined' => 'Joined a team',
        ];

        return $descriptions[$type] ?? $type;
    }
}
