<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Share;
use App\Models\Snippet;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    /**
     * Share types
     */
    const SHARE_TYPES = ['link', 'user', 'team', 'email'];
    const PERMISSIONS = ['view', 'edit'];

    /**
     * Get shares for a snippet (owner only)
     *
     * @param Request $request
     * @param string $snippetId
     * @return JsonResponse
     */
    public function index(Request $request, string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Only snippet owner can view shares
        if (!$snippet->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view shares for this snippet.',
            ], 403);
        }

        $query = Share::where('snippet_id', $snippetId)
            ->with([
                'sharedWith:id,username,full_name,avatar_url',
                'team:id,name,slug',
            ]);

        // Filter by type
        if ($request->has('type')) {
            $type = $request->get('type');
            if (in_array($type, self::SHARE_TYPES)) {
                $query->ofType($type);
            }
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'last_accessed_at', 'access_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $shares = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Shares retrieved successfully.',
            'data' => $shares->items(),
            'meta' => [
                'current_page' => $shares->currentPage(),
                'last_page' => $shares->lastPage(),
                'per_page' => $shares->perPage(),
                'total' => $shares->total(),
            ],
        ]);
    }

    /**
     * Create a new share
     *
     * @param Request $request
     * @param string $snippetId
     * @return JsonResponse
     */
    public function store(Request $request, string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Only snippet owner can create shares
        if (!$snippet->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to share this snippet.',
            ], 403);
        }

        $rules = [
            'share_type' => 'required|string|in:' . implode(',', self::SHARE_TYPES),
            'permission' => 'sometimes|string|in:' . implode(',', self::PERMISSIONS),
            'expires_at' => 'nullable|date|after:now',
        ];

        // Add type-specific validation rules
        $shareType = $request->get('share_type');
        switch ($shareType) {
            case 'user':
                $rules['user_id'] = 'required|uuid|exists:users,id';
                break;
            case 'team':
                $rules['team_id'] = 'required|uuid|exists:teams,id';
                break;
            case 'email':
                $rules['email'] = 'required|email|max:255';
                break;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check for duplicate shares
        $existingShare = null;
        switch ($shareType) {
            case 'user':
                // Cannot share with yourself
                if ($request->user_id === Auth::id()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You cannot share a snippet with yourself.',
                    ], 422);
                }
                $existingShare = Share::where('snippet_id', $snippetId)
                    ->where('shared_with', $request->user_id)
                    ->where('share_type', 'user')
                    ->first();
                break;
            case 'team':
                // Verify user has access to the team
                $team = Team::find($request->team_id);
                if (!$team || !$team->hasMember(Auth::user())) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have access to this team.',
                    ], 403);
                }
                $existingShare = Share::where('snippet_id', $snippetId)
                    ->where('team_id', $request->team_id)
                    ->where('share_type', 'team')
                    ->first();
                break;
            case 'email':
                $existingShare = Share::where('snippet_id', $snippetId)
                    ->where('email', $request->email)
                    ->where('share_type', 'email')
                    ->first();
                break;
        }

        if ($existingShare && $existingShare->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'This snippet is already shared with this recipient.',
                'data' => $existingShare,
            ], 422);
        }

        // Create share
        $shareData = [
            'snippet_id' => $snippetId,
            'shared_by' => Auth::id(),
            'share_type' => $shareType,
            'permission' => $request->get('permission', 'view'),
            'expires_at' => $request->expires_at,
            'is_active' => true,
        ];

        // Add type-specific data
        switch ($shareType) {
            case 'user':
                $shareData['shared_with'] = $request->user_id;
                break;
            case 'team':
                $shareData['team_id'] = $request->team_id;
                break;
            case 'email':
                $shareData['email'] = $request->email;
                $shareData['share_token'] = Str::random(64);
                break;
            case 'link':
                $shareData['share_token'] = Str::random(64);
                break;
        }

        $share = Share::create($shareData);
        $share->load(['sharedWith:id,username,full_name,avatar_url', 'team:id,name,slug']);

        return response()->json([
            'success' => true,
            'message' => 'Snippet shared successfully.',
            'data' => $share,
        ], 201);
    }

    /**
     * Get a specific share
     *
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $share = Share::with([
            'snippet:id,title,slug,user_id',
            'sharedWith:id,username,full_name,avatar_url',
            'team:id,name,slug',
        ])->find($id);

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Share not found.',
            ], 404);
        }

        // Only snippet owner can view share details
        if ($share->shared_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this share.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Share retrieved successfully.',
            'data' => $share,
        ]);
    }

    /**
     * Update a share
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $share = Share::find($id);

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Share not found.',
            ], 404);
        }

        // Only snippet owner can update share
        if ($share->shared_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this share.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'permission' => 'sometimes|string|in:' . implode(',', self::PERMISSIONS),
            'expires_at' => 'nullable|date|after:now',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $share->update($request->only(['permission', 'expires_at', 'is_active']));
        $share->load(['sharedWith:id,username,full_name,avatar_url', 'team:id,name,slug']);

        return response()->json([
            'success' => true,
            'message' => 'Share updated successfully.',
            'data' => $share,
        ]);
    }

    /**
     * Delete a share
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $share = Share::find($id);

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Share not found.',
            ], 404);
        }

        // Only snippet owner can delete share
        if ($share->shared_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this share.',
            ], 403);
        }

        $share->delete();

        return response()->json([
            'success' => true,
            'message' => 'Share deleted successfully.',
        ]);
    }

    /**
     * Regenerate share token
     *
     * @param string $id
     * @return JsonResponse
     */
    public function regenerateToken(string $id): JsonResponse
    {
        $share = Share::find($id);

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Share not found.',
            ], 404);
        }

        // Only snippet owner can regenerate token
        if ($share->shared_by !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to regenerate this share token.',
            ], 403);
        }

        // Only link and email shares have tokens
        if (!in_array($share->share_type, ['link', 'email'])) {
            return response()->json([
                'success' => false,
                'message' => 'This share type does not have a token.',
            ], 422);
        }

        $share->update([
            'share_token' => Str::random(64),
            'access_count' => 0,
            'last_accessed_at' => null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Share token regenerated successfully.',
            'data' => $share,
        ]);
    }

    /**
     * Access a shared snippet via token (public endpoint)
     *
     * @param string $token
     * @return JsonResponse
     */
    public function accessByToken(string $token): JsonResponse
    {
        $share = Share::with([
            'snippet' => function ($query) {
                $query->with(['user:id,username,full_name,avatar_url', 'language', 'tags']);
            },
        ])->where('share_token', $token)->first();

        if (!$share) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid share link.',
            ], 404);
        }

        if (!$share->isValid()) {
            return response()->json([
                'success' => false,
                'message' => $share->isExpired() ? 'This share link has expired.' : 'This share link is no longer active.',
            ], 403);
        }

        // Record access
        $share->recordAccess();

        return response()->json([
            'success' => true,
            'message' => 'Shared snippet retrieved successfully.',
            'data' => [
                'snippet' => $share->snippet,
                'permission' => $share->permission,
                'shared_by' => [
                    'id' => $share->sharedBy->id,
                    'username' => $share->sharedBy->username,
                    'full_name' => $share->sharedBy->full_name,
                ],
                'expires_at' => $share->expires_at,
            ],
        ]);
    }

    /**
     * Get snippets shared with the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sharedWithMe(Request $request): JsonResponse
    {
        $user = Auth::user();

        // Get user's team IDs
        $teamIds = $user->teams()->pluck('teams.id');

        $query = Share::with([
            'snippet' => function ($query) {
                $query->with(['user:id,username,full_name,avatar_url', 'language']);
            },
            'sharedBy:id,username,full_name,avatar_url',
            'team:id,name,slug',
        ])
            ->where(function ($q) use ($user, $teamIds) {
                $q->where('shared_with', $user->id)
                    ->orWhereIn('team_id', $teamIds)
                    ->orWhere('email', $user->email);
            })
            ->active();

        // Filter by permission
        if ($request->has('permission')) {
            $permission = $request->get('permission');
            if (in_array($permission, self::PERMISSIONS)) {
                $query->where('permission', $permission);
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'last_accessed_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $shares = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Shared snippets retrieved successfully.',
            'data' => $shares->items(),
            'meta' => [
                'current_page' => $shares->currentPage(),
                'last_page' => $shares->lastPage(),
                'per_page' => $shares->perPage(),
                'total' => $shares->total(),
            ],
        ]);
    }

    /**
     * Get all snippets I have shared
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function sharedByMe(Request $request): JsonResponse
    {
        $query = Share::with([
            'snippet:id,title,slug',
            'sharedWith:id,username,full_name,avatar_url',
            'team:id,name,slug',
        ])
            ->where('shared_by', Auth::id());

        // Filter by type
        if ($request->has('type')) {
            $type = $request->get('type');
            if (in_array($type, self::SHARE_TYPES)) {
                $query->ofType($type);
            }
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'last_accessed_at', 'access_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $shares = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Your shared snippets retrieved successfully.',
            'data' => $shares->items(),
            'meta' => [
                'current_page' => $shares->currentPage(),
                'last_page' => $shares->lastPage(),
                'per_page' => $shares->perPage(),
                'total' => $shares->total(),
            ],
        ]);
    }

    /**
     * Revoke all shares for a snippet
     *
     * @param string $snippetId
     * @return JsonResponse
     */
    public function revokeAll(string $snippetId): JsonResponse
    {
        $snippet = Snippet::find($snippetId);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        // Only snippet owner can revoke shares
        if (!$snippet->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to revoke shares for this snippet.',
            ], 403);
        }

        $updated = Share::where('snippet_id', $snippetId)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        return response()->json([
            'success' => true,
            'message' => "Revoked {$updated} shares.",
            'data' => [
                'revoked_count' => $updated,
            ],
        ]);
    }
}
