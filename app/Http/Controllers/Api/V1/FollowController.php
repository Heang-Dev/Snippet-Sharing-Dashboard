<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FollowController extends Controller
{
    /**
     * Follow a user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function follow(string $userId): JsonResponse
    {
        $user = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Cannot follow yourself
        if ($user->id === $targetUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 422);
        }

        // Check if already following
        if ($user->isFollowing($targetUser)) {
            return response()->json([
                'success' => false,
                'message' => 'You are already following this user.',
            ], 422);
        }

        // Create follow relationship
        $user->following()->attach($targetUser->id, [
            'id' => \Illuminate\Support\Str::uuid(),
            'notification_enabled' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully followed user.',
            'data' => [
                'following' => true,
                'user' => [
                    'id' => $targetUser->id,
                    'username' => $targetUser->username,
                    'full_name' => $targetUser->full_name,
                    'avatar_url' => $targetUser->avatar_url,
                ],
            ],
        ], 201);
    }

    /**
     * Unfollow a user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function unfollow(string $userId): JsonResponse
    {
        $user = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Cannot unfollow yourself
        if ($user->id === $targetUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot unfollow yourself.',
            ], 422);
        }

        // Check if not following
        if (!$user->isFollowing($targetUser)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not following this user.',
            ], 422);
        }

        // Remove follow relationship
        $user->following()->detach($targetUser->id);

        return response()->json([
            'success' => true,
            'message' => 'Successfully unfollowed user.',
            'data' => [
                'following' => false,
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
     * Toggle follow status for a user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function toggle(string $userId): JsonResponse
    {
        $user = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Cannot follow yourself
        if ($user->id === $targetUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot follow yourself.',
            ], 422);
        }

        $isFollowing = $user->isFollowing($targetUser);

        if ($isFollowing) {
            $user->following()->detach($targetUser->id);
            $message = 'Successfully unfollowed user.';
        } else {
            $user->following()->attach($targetUser->id, [
                'id' => \Illuminate\Support\Str::uuid(),
                'notification_enabled' => true,
            ]);
            $message = 'Successfully followed user.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'following' => !$isFollowing,
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
     * Check if current user is following a specific user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function check(string $userId): JsonResponse
    {
        $user = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $isFollowing = $user->isFollowing($targetUser);
        $isFollowedBy = $targetUser->isFollowing($user);

        return response()->json([
            'success' => true,
            'message' => 'Follow status retrieved successfully.',
            'data' => [
                'following' => $isFollowing,
                'followed_by' => $isFollowedBy,
                'mutual' => $isFollowing && $isFollowedBy,
            ],
        ]);
    }

    /**
     * Get followers of a user
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function followers(Request $request, string $userId): JsonResponse
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $query = $targetUser->followers()
            ->select('users.id', 'users.username', 'users.full_name', 'users.avatar_url', 'users.bio');

        // Search by username or full_name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                    ->orWhere('users.full_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'username', 'full_name'];

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'created_at') {
                $query->orderBy('follows.created_at', $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy("users.{$sortBy}", $sortOrder === 'asc' ? 'asc' : 'desc');
            }
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $followers = $query->paginate($perPage);

        // Add following status for current user if authenticated
        $currentUser = Auth::user();
        $followersData = collect($followers->items())->map(function ($follower) use ($currentUser) {
            $followerArray = $follower->toArray();
            if ($currentUser) {
                $followerArray['is_following'] = $currentUser->isFollowing($follower);
                $followerArray['is_followed_by'] = $follower->isFollowing($currentUser);
            }
            $followerArray['followed_at'] = $follower->pivot->created_at;
            return $followerArray;
        });

        return response()->json([
            'success' => true,
            'message' => 'Followers retrieved successfully.',
            'data' => $followersData,
            'meta' => [
                'current_page' => $followers->currentPage(),
                'last_page' => $followers->lastPage(),
                'per_page' => $followers->perPage(),
                'total' => $followers->total(),
            ],
        ]);
    }

    /**
     * Get users that a user is following
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function following(Request $request, string $userId): JsonResponse
    {
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $query = $targetUser->following()
            ->select('users.id', 'users.username', 'users.full_name', 'users.avatar_url', 'users.bio');

        // Search by username or full_name
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('users.username', 'like', "%{$search}%")
                    ->orWhere('users.full_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'username', 'full_name'];

        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'created_at') {
                $query->orderBy('follows.created_at', $sortOrder === 'asc' ? 'asc' : 'desc');
            } else {
                $query->orderBy("users.{$sortBy}", $sortOrder === 'asc' ? 'asc' : 'desc');
            }
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $following = $query->paginate($perPage);

        // Add following status for current user if authenticated
        $currentUser = Auth::user();
        $followingData = collect($following->items())->map(function ($followed) use ($currentUser) {
            $followedArray = $followed->toArray();
            if ($currentUser) {
                $followedArray['is_following'] = $currentUser->isFollowing($followed);
                $followedArray['is_followed_by'] = $followed->isFollowing($currentUser);
            }
            $followedArray['followed_at'] = $followed->pivot->created_at;
            return $followedArray;
        });

        return response()->json([
            'success' => true,
            'message' => 'Following list retrieved successfully.',
            'data' => $followingData,
            'meta' => [
                'current_page' => $following->currentPage(),
                'last_page' => $following->lastPage(),
                'per_page' => $following->perPage(),
                'total' => $following->total(),
            ],
        ]);
    }

    /**
     * Get current user's followers
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myFollowers(Request $request): JsonResponse
    {
        return $this->followers($request, Auth::id());
    }

    /**
     * Get users current user is following
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function myFollowing(Request $request): JsonResponse
    {
        return $this->following($request, Auth::id());
    }

    /**
     * Get follow statistics for a user
     *
     * @param string $userId
     * @return JsonResponse
     */
    public function stats(string $userId): JsonResponse
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $followersCount = $user->followers()->count();
        $followingCount = $user->following()->count();

        // Get relationship with current user
        $currentUser = Auth::user();
        $isFollowing = false;
        $isFollowedBy = false;

        if ($currentUser && $currentUser->id !== $user->id) {
            $isFollowing = $currentUser->isFollowing($user);
            $isFollowedBy = $user->isFollowing($currentUser);
        }

        return response()->json([
            'success' => true,
            'message' => 'Follow statistics retrieved successfully.',
            'data' => [
                'user_id' => $user->id,
                'username' => $user->username,
                'followers_count' => $followersCount,
                'following_count' => $followingCount,
                'is_following' => $isFollowing,
                'is_followed_by' => $isFollowedBy,
                'is_mutual' => $isFollowing && $isFollowedBy,
            ],
        ]);
    }

    /**
     * Update notification settings for a follow relationship
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function updateNotificationSettings(Request $request, string $userId): JsonResponse
    {
        $user = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Check if following
        if (!$user->isFollowing($targetUser)) {
            return response()->json([
                'success' => false,
                'message' => 'You are not following this user.',
            ], 422);
        }

        $validator = Validator::make($request->all(), [
            'notification_enabled' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update notification setting
        $user->following()->updateExistingPivot($targetUser->id, [
            'notification_enabled' => $request->notification_enabled,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully.',
            'data' => [
                'user_id' => $targetUser->id,
                'notification_enabled' => $request->notification_enabled,
            ],
        ]);
    }

    /**
     * Get suggested users to follow
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function suggestions(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min($request->get('limit', 10), 50);

        // Get IDs of users already following
        $followingIds = $user->following()->pluck('users.id')->toArray();
        $followingIds[] = $user->id; // Exclude self

        // Get suggested users based on:
        // 1. Users followed by users you follow (mutual connections)
        // 2. Popular users (most followers)
        // 3. Recently active users

        $mutualFollowsSuggestions = DB::table('follows')
            ->join('users', 'users.id', '=', 'follows.following_id')
            ->whereIn('follows.follower_id', $followingIds)
            ->whereNotIn('follows.following_id', $followingIds)
            ->whereNull('users.deleted_at')
            ->where('users.is_active', true)
            ->select('users.id', DB::raw('COUNT(*) as mutual_count'))
            ->groupBy('users.id')
            ->orderByDesc('mutual_count')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        // If not enough mutual suggestions, add popular users
        if (count($mutualFollowsSuggestions) < $limit) {
            $remaining = $limit - count($mutualFollowsSuggestions);
            $excludeIds = array_merge($followingIds, $mutualFollowsSuggestions);

            $popularUsers = User::whereNotIn('id', $excludeIds)
                ->where('is_active', true)
                ->withCount('followers')
                ->orderByDesc('followers_count')
                ->limit($remaining)
                ->pluck('id')
                ->toArray();

            $mutualFollowsSuggestions = array_merge($mutualFollowsSuggestions, $popularUsers);
        }

        // Fetch full user data
        $suggestions = User::whereIn('id', $mutualFollowsSuggestions)
            ->select('id', 'username', 'full_name', 'avatar_url', 'bio')
            ->withCount(['followers', 'snippets'])
            ->get()
            ->map(function ($suggestedUser) use ($user) {
                // Get mutual followers count
                $mutualCount = DB::table('follows as f1')
                    ->join('follows as f2', 'f1.following_id', '=', 'f2.follower_id')
                    ->where('f1.follower_id', $user->id)
                    ->where('f2.following_id', $suggestedUser->id)
                    ->count();

                return [
                    'id' => $suggestedUser->id,
                    'username' => $suggestedUser->username,
                    'full_name' => $suggestedUser->full_name,
                    'avatar_url' => $suggestedUser->avatar_url,
                    'bio' => $suggestedUser->bio,
                    'followers_count' => $suggestedUser->followers_count,
                    'snippets_count' => $suggestedUser->snippets_count,
                    'mutual_followers_count' => $mutualCount,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'Follow suggestions retrieved successfully.',
            'data' => $suggestions,
        ]);
    }

    /**
     * Get mutual followers between current user and another user
     *
     * @param Request $request
     * @param string $userId
     * @return JsonResponse
     */
    public function mutualFollowers(Request $request, string $userId): JsonResponse
    {
        $currentUser = Auth::user();
        $targetUser = User::find($userId);

        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        if ($currentUser->id === $targetUser->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot get mutual followers with yourself.',
            ], 422);
        }

        // Get users that both current user and target user follow
        $currentUserFollowing = $currentUser->following()->pluck('users.id');

        $query = $targetUser->followers()
            ->whereIn('users.id', $currentUserFollowing)
            ->select('users.id', 'users.username', 'users.full_name', 'users.avatar_url', 'users.bio');

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $mutuals = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Mutual followers retrieved successfully.',
            'data' => $mutuals->items(),
            'meta' => [
                'current_page' => $mutuals->currentPage(),
                'last_page' => $mutuals->lastPage(),
                'per_page' => $mutuals->perPage(),
                'total' => $mutuals->total(),
            ],
        ]);
    }
}
