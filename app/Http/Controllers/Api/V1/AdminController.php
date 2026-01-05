<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Comment;
use App\Models\Language;
use App\Models\Snippet;
use App\Models\Tag;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    /**
     * Middleware to check admin access
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user() || !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied. Admin privileges required.',
                ], 403);
            }
            return $next($request);
        });
    }

    /**
     * Get dashboard overview statistics
     *
     * @return JsonResponse
     */
    public function dashboard(): JsonResponse
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'active' => User::where('is_active', true)->count(),
                'admins' => User::where('is_admin', true)->count(),
                'new_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
                'new_this_month' => User::where('created_at', '>=', now()->subMonth())->count(),
            ],
            'snippets' => [
                'total' => Snippet::count(),
                'public' => Snippet::where('privacy', 'public')->count(),
                'private' => Snippet::where('privacy', 'private')->count(),
                'new_this_week' => Snippet::where('created_at', '>=', now()->subWeek())->count(),
                'new_this_month' => Snippet::where('created_at', '>=', now()->subMonth())->count(),
            ],
            'collections' => [
                'total' => Collection::count(),
                'public' => Collection::where('privacy', 'public')->count(),
            ],
            'comments' => [
                'total' => Comment::count(),
                'new_this_week' => Comment::where('created_at', '>=', now()->subWeek())->count(),
            ],
            'teams' => [
                'total' => Team::count(),
                'active' => Team::where('is_active', true)->count(),
            ],
            'languages' => Language::count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
        ];

        return response()->json([
            'success' => true,
            'message' => 'Dashboard statistics retrieved successfully.',
            'data' => $stats,
        ]);
    }

    /**
     * Get all users with admin filters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $query = User::withCount(['snippets', 'collections', 'comments', 'followers', 'following']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Filter by admin status
        if ($request->has('is_admin')) {
            $query->where('is_admin', $request->boolean('is_admin'));
        }

        // Filter by email verification
        if ($request->has('email_verified')) {
            if ($request->boolean('email_verified')) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'username', 'email', 'last_login_at', 'snippets_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $users = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Users retrieved successfully.',
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get user details for admin
     *
     * @param string $id
     * @return JsonResponse
     */
    public function showUser(string $id): JsonResponse
    {
        $user = User::withCount(['snippets', 'collections', 'comments', 'followers', 'following', 'teams'])
            ->find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Get recent activity
        $recentSnippets = Snippet::where('user_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'title', 'slug', 'privacy', 'created_at']);

        $recentComments = Comment::where('user_id', $id)
            ->with('snippet:id,title,slug')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'snippet_id', 'content', 'created_at']);

        return response()->json([
            'success' => true,
            'message' => 'User details retrieved successfully.',
            'data' => [
                'user' => $user,
                'recent_snippets' => $recentSnippets,
                'recent_comments' => $recentComments,
            ],
        ]);
    }

    /**
     * Update user (admin action)
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateUser(Request $request, string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_active' => 'sometimes|boolean',
            'is_admin' => 'sometimes|boolean',
            'email_verified_at' => 'sometimes|nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prevent self-demotion from admin
        if ($user->id === Auth::id() && $request->has('is_admin') && !$request->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot remove your own admin privileges.',
            ], 422);
        }

        $user->update($request->only(['is_active', 'is_admin', 'email_verified_at']));

        // Log the action
        AuditLog::log('user_updated', 'user', $user->id, null, $request->only(['is_active', 'is_admin']), [
            'admin_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully.',
            'data' => $user->fresh(),
        ]);
    }

    /**
     * Delete user (admin action)
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteUser(string $id): JsonResponse
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        // Prevent self-deletion
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        // Log before deletion
        AuditLog::log('user_deleted', 'user', $user->id, [
            'username' => $user->username,
            'email' => $user->email,
        ], null, [
            'admin_id' => Auth::id(),
        ]);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    /**
     * Get all snippets with admin filters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function snippets(Request $request): JsonResponse
    {
        $query = Snippet::with([
            'user:id,username,full_name,avatar_url',
            'language:id,name,slug',
        ])->withCount(['favorites', 'comments', 'forks']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by privacy
        if ($request->has('privacy')) {
            $query->where('privacy', $request->get('privacy'));
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->get('user_id'));
        }

        // Filter by featured
        if ($request->has('is_featured')) {
            $query->where('is_featured', $request->boolean('is_featured'));
        }

        // Filter by language
        if ($request->has('language_id')) {
            $query->where('language_id', $request->get('language_id'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'title', 'views_count', 'favorites_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $snippets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Snippets retrieved successfully.',
            'data' => $snippets->items(),
            'meta' => [
                'current_page' => $snippets->currentPage(),
                'last_page' => $snippets->lastPage(),
                'per_page' => $snippets->perPage(),
                'total' => $snippets->total(),
            ],
        ]);
    }

    /**
     * Update snippet (admin action)
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateSnippet(Request $request, string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'is_featured' => 'sometimes|boolean',
            'privacy' => 'sometimes|in:public,private,unlisted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $oldValues = $snippet->only(['is_featured', 'privacy']);
        $snippet->update($request->only(['is_featured', 'privacy']));

        // Log the action
        AuditLog::log('snippet_updated', 'snippet', $snippet->id, $oldValues, $request->only(['is_featured', 'privacy']), [
            'admin_id' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Snippet updated successfully.',
            'data' => $snippet->fresh(),
        ]);
    }

    /**
     * Delete snippet (admin action)
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteSnippet(string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        AuditLog::log('snippet_deleted', 'snippet', $snippet->id, [
            'title' => $snippet->title,
            'user_id' => $snippet->user_id,
        ], null, [
            'admin_id' => Auth::id(),
        ]);

        $snippet->delete();

        return response()->json([
            'success' => true,
            'message' => 'Snippet deleted successfully.',
        ]);
    }

    /**
     * Delete comment (admin action)
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteComment(string $id): JsonResponse
    {
        $comment = Comment::find($id);

        if (!$comment) {
            return response()->json([
                'success' => false,
                'message' => 'Comment not found.',
            ], 404);
        }

        AuditLog::log('comment_deleted', 'comment', $comment->id, [
            'content' => $comment->content,
            'user_id' => $comment->user_id,
            'snippet_id' => $comment->snippet_id,
        ], null, [
            'admin_id' => Auth::id(),
        ]);

        // Delete replies if this is a root comment
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
     * Get audit logs
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function auditLogs(Request $request): JsonResponse
    {
        $query = AuditLog::with('user:id,username,full_name,avatar_url');

        // Filter by user
        if ($request->has('user_id')) {
            $query->forUser($request->get('user_id'));
        }

        // Filter by action
        if ($request->has('action')) {
            $query->ofAction($request->get('action'));
        }

        // Filter by resource type
        if ($request->has('resource_type')) {
            $query->forResource($request->get('resource_type'), $request->get('resource_id'));
        }

        // Filter by date range
        if ($request->has('from')) {
            $query->where('created_at', '>=', $request->get('from'));
        }
        if ($request->has('to')) {
            $query->where('created_at', '<=', $request->get('to'));
        }

        // Sort
        $query->orderBy('created_at', 'desc');

        // Pagination
        $perPage = min($request->get('per_page', 50), 100);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Audit logs retrieved successfully.',
            'data' => $logs->items(),
            'meta' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
            ],
        ]);
    }

    /**
     * Get system statistics over time
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function analytics(Request $request): JsonResponse
    {
        $days = min($request->get('days', 30), 365);
        $startDate = now()->subDays($days);

        // Users growth
        $usersGrowth = User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Snippets growth
        $snippetsGrowth = Snippet::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top languages
        $topLanguages = Snippet::select('language_id', DB::raw('COUNT(*) as count'))
            ->with('language:id,name,slug')
            ->groupBy('language_id')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Most active users
        $mostActiveUsers = User::select('id', 'username', 'full_name', 'avatar_url')
            ->withCount(['snippets', 'comments'])
            ->orderByRaw('snippets_count + comments_count DESC')
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Analytics retrieved successfully.',
            'data' => [
                'period_days' => $days,
                'users_growth' => $usersGrowth,
                'snippets_growth' => $snippetsGrowth,
                'top_languages' => $topLanguages,
                'most_active_users' => $mostActiveUsers,
            ],
        ]);
    }

    /**
     * Manage languages (CRUD)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createLanguage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50|unique:languages,name',
            'slug' => 'required|string|max:50|unique:languages,slug',
            'file_extension' => 'nullable|string|max:20',
            'mime_type' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $language = Language::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Language created successfully.',
            'data' => $language,
        ], 201);
    }

    /**
     * Update language
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateLanguage(Request $request, string $id): JsonResponse
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:50|unique:languages,name,' . $id,
            'slug' => 'sometimes|string|max:50|unique:languages,slug,' . $id,
            'file_extension' => 'nullable|string|max:20',
            'mime_type' => 'nullable|string|max:100',
            'icon' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:20',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $language->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Language updated successfully.',
            'data' => $language,
        ]);
    }

    /**
     * Delete language
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteLanguage(string $id): JsonResponse
    {
        $language = Language::find($id);

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
            ], 404);
        }

        // Check if language is in use
        $snippetCount = Snippet::where('language_id', $id)->count();
        if ($snippetCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete language. It is used by {$snippetCount} snippets.",
            ], 422);
        }

        $language->delete();

        return response()->json([
            'success' => true,
            'message' => 'Language deleted successfully.',
        ]);
    }

    /**
     * Manage categories (CRUD)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function createCategory(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100|unique:categories,name',
            'slug' => 'required|string|max:100|unique:categories,slug',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|uuid|exists:categories,id',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $category = Category::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category,
        ], 201);
    }

    /**
     * Update category
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function updateCategory(Request $request, string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:100|unique:categories,name,' . $id,
            'slug' => 'sometimes|string|max:100|unique:categories,slug,' . $id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'parent_id' => 'nullable|uuid|exists:categories,id',
            'sort_order' => 'sometimes|integer|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Prevent self-referencing
        if ($request->has('parent_id') && $request->parent_id === $id) {
            return response()->json([
                'success' => false,
                'message' => 'Category cannot be its own parent.',
            ], 422);
        }

        $category->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully.',
            'data' => $category,
        ]);
    }

    /**
     * Delete category
     *
     * @param string $id
     * @return JsonResponse
     */
    public function deleteCategory(string $id): JsonResponse
    {
        $category = Category::find($id);

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        // Check for children
        $childCount = Category::where('parent_id', $id)->count();
        if ($childCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete category. It has {$childCount} child categories.",
            ], 422);
        }

        // Check if category is in use
        $snippetCount = Snippet::where('category_id', $id)->count();
        if ($snippetCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete category. It is used by {$snippetCount} snippets.",
            ], 422);
        }

        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully.',
        ]);
    }
}
