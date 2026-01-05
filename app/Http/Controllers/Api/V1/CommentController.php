<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Snippet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    /**
     * Get comments for a snippet
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

        // Check if user can view this snippet's comments
        $user = Auth::user();
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view comments for this snippet.',
            ], 403);
        }

        $query = Comment::where('snippet_id', $snippetId)
            ->with(['user:id,username,full_name,avatar_url']);

        // Get only root comments or all comments
        if (!$request->has('all') || !$request->boolean('all')) {
            $query->roots()->with(['replies' => function ($q) {
                $q->with(['user:id,username,full_name,avatar_url'])
                    ->orderBy('created_at', 'asc');
            }]);
        }

        // Sort
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy('created_at', $sortOrder === 'asc' ? 'asc' : 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $comments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Comments retrieved successfully.',
            'data' => $comments->items(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Create a new comment
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

        // Check if user can comment on this snippet
        $user = Auth::user();
        if (!$snippet->isPublic() && !$snippet->isOwnedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to comment on this snippet.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:1|max:5000',
            'parent_id' => 'nullable|uuid|exists:comments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If parent_id provided, validate it belongs to the same snippet
        if ($request->parent_id) {
            $parentComment = Comment::find($request->parent_id);
            if (!$parentComment || $parentComment->snippet_id !== $snippetId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid parent comment.',
                ], 422);
            }
        }

        $comment = Comment::create([
            'snippet_id' => $snippetId,
            'user_id' => Auth::id(),
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_edited' => false,
        ]);

        $comment->load(['user:id,username,full_name,avatar_url']);

        return response()->json([
            'success' => true,
            'message' => 'Comment created successfully.',
            'data' => $comment,
        ], 201);
    }

    /**
     * Get a specific comment
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $query = Comment::with(['user:id,username,full_name,avatar_url', 'snippet:id,title,slug,user_id,privacy']);

        // Include replies if requested
        if ($request->has('with_replies') && $request->boolean('with_replies')) {
            $query->with(['replies' => function ($q) {
                $q->with(['user:id,username,full_name,avatar_url'])
                    ->orderBy('created_at', 'asc');
            }]);
        }

        $comment = $query->find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        // Check privacy of the snippet
        $user = Auth::user();
        $snippet = $comment->snippet;
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this comment.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment retrieved successfully.',
            'data' => $comment,
        ]);
    }

    /**
     * Update a comment
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        // Check ownership
        if (!$comment->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this comment.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:1|max:5000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $comment->update([
            'content' => $request->content,
            'is_edited' => true,
        ]);

        $comment->load(['user:id,username,full_name,avatar_url']);

        return response()->json([
            'success' => true,
            'message' => 'Comment updated successfully.',
            'data' => $comment,
        ]);
    }

    /**
     * Delete a comment
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        $user = Auth::user();

        // Check ownership or snippet ownership (snippet owner can delete any comment)
        $snippet = $comment->snippet;
        if (!$comment->isOwnedBy($user) && !$snippet->isOwnedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this comment.',
            ], 403);
        }

        // Delete replies first if this is a root comment
        if (!$comment->isReply()) {
            $comment->replies()->delete();
        }

        $comment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully.',
        ]);
    }

    /**
     * Get replies to a comment
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function replies(Request $request, string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        // Check privacy of the snippet
        $user = Auth::user();
        $snippet = $comment->snippet;
        if (!$snippet->isPublic() && (!$user || !$snippet->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view these replies.',
            ], 403);
        }

        $query = Comment::where('parent_id', $id)
            ->with(['user:id,username,full_name,avatar_url'])
            ->orderBy('created_at', 'asc');

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $replies = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Replies retrieved successfully.',
            'data' => $replies->items(),
            'meta' => [
                'current_page' => $replies->currentPage(),
                'last_page' => $replies->lastPage(),
                'per_page' => $replies->perPage(),
                'total' => $replies->total(),
            ],
        ]);
    }

    /**
     * Get user's comments
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userComments(Request $request): JsonResponse
    {
        $query = Comment::where('user_id', Auth::id())
            ->with([
                'snippet:id,title,slug,user_id,privacy',
                'parent:id,content,user_id',
            ]);

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'updated_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $comments = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Your comments retrieved successfully.',
            'data' => $comments->items(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'per_page' => $comments->perPage(),
                'total' => $comments->total(),
            ],
        ]);
    }
}
