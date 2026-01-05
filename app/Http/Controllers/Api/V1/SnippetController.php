<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Snippet;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SnippetController extends Controller
{
    /**
     * Get all snippets for the authenticated user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Snippet::where('user_id', $user->id)
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url']);

        // Filter by visibility
        if ($request->has('visibility') && in_array($request->visibility, ['public', 'private', 'team', 'unlisted'])) {
            $query->where('visibility', $request->visibility);
        }

        // Filter by language
        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by pinned
        if ($request->has('pinned')) {
            $query->where('is_pinned', $request->boolean('pinned'));
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'updated_at', 'title', 'views_count', 'favorites_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Pinned snippets first
        $query->orderByDesc('is_pinned');

        // Paginate
        $perPage = min($request->get('per_page', 15), 100);
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
     * Get public snippets (for explore/discovery)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $query = Snippet::public()
            ->notExpired()
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url']);

        // Filter by language
        if ($request->has('language_id')) {
            $query->where('language_id', $request->language_id);
        }

        // Filter by language slug
        if ($request->has('language')) {
            $query->whereHas('language', function ($q) use ($request) {
                $q->where('slug', $request->language);
            });
        }

        // Filter by category
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by category slug
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'updated_at', 'title', 'views_count', 'favorites_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Paginate
        $perPage = min($request->get('per_page', 15), 100);
        $snippets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Public snippets retrieved successfully.',
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
     * Get trending snippets
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trending(Request $request): JsonResponse
    {
        $days = $request->get('days', 7);
        $limit = min($request->get('limit', 20), 100);

        $snippets = Snippet::public()
            ->notExpired()
            ->where('created_at', '>=', now()->subDays($days))
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url'])
            ->orderByDesc('views_count')
            ->orderByDesc('favorites_count')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Trending snippets retrieved successfully.',
            'data' => $snippets,
        ]);
    }

    /**
     * Get featured snippets
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 10), 50);

        $snippets = Snippet::public()
            ->notExpired()
            ->where('is_pinned', true)
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Featured snippets retrieved successfully.',
            'data' => $snippets,
        ]);
    }

    /**
     * Create a new snippet
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'code' => 'required|string|max:1000000', // ~1MB of code
            'language_id' => 'nullable|uuid|exists:languages,id',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'visibility' => ['required', Rule::in(['public', 'private', 'team', 'unlisted'])],
            'file_name' => 'nullable|string|max:255',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'expires_at' => 'nullable|date|after:now',
            'team_id' => 'nullable|uuid|exists:teams,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $user = $request->user();

        // If team visibility, validate team membership
        if ($request->visibility === 'team') {
            if (!$request->team_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team ID is required for team visibility.',
                ], 422);
            }

            $isMember = $user->teams()->where('teams.id', $request->team_id)->exists();
            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this team.',
                ], 403);
            }
        }

        DB::beginTransaction();

        try {
            // Create snippet
            $snippet = Snippet::create([
                'user_id' => $user->id,
                'title' => $request->title,
                'description' => $request->description,
                'code' => $request->code,
                'language_id' => $request->language_id,
                'category_id' => $request->category_id,
                'visibility' => $request->visibility,
                'file_name' => $request->file_name,
                'expires_at' => $request->expires_at,
                'team_id' => $request->visibility === 'team' ? $request->team_id : null,
                'version' => 1,
            ]);

            // Handle tags
            if ($request->has('tags') && is_array($request->tags)) {
                $tagIds = [];
                foreach ($request->tags as $tagName) {
                    $tagName = trim($tagName);
                    if (empty($tagName)) continue;

                    $tag = Tag::firstOrCreate(
                        ['name' => strtolower($tagName)],
                        ['name' => strtolower($tagName)]
                    );
                    $tag->incrementUsageCount();
                    $tagIds[] = $tag->id;
                }
                $snippet->tags()->sync($tagIds);
            }

            // Update user's snippet count
            $user->increment('snippets_count');

            // Update language snippet count
            if ($snippet->language_id) {
                $snippet->language->increment('snippet_count');
            }

            // Update category snippet count
            if ($snippet->category_id) {
                $snippet->category->increment('snippet_count');
            }

            DB::commit();

            // Load relationships
            $snippet->load(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url']);

            return response()->json([
                'success' => true,
                'message' => 'Snippet created successfully.',
                'data' => $snippet,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to create snippet.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Get a specific snippet
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $snippet = Snippet::where('slug', $slug)
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url', 'forkedFrom:id,title,slug'])
            ->first();

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check if user can view this snippet
        if (!$snippet->canBeViewedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet.',
            ], 403);
        }

        // Increment view count (only for non-owners)
        if (!$user || !$snippet->isOwnedBy($user)) {
            $snippet->incrementViewCount();
        }

        // Add additional info for authenticated users
        $responseData = $snippet->toArray();

        if ($user) {
            $responseData['is_favorited'] = $snippet->favoritedBy()->where('user_id', $user->id)->exists();
            $responseData['is_owner'] = $snippet->isOwnedBy($user);
        }

        return response()->json([
            'success' => true,
            'message' => 'Snippet retrieved successfully.',
            'data' => $responseData,
        ]);
    }

    /**
     * Update a snippet
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check ownership
        if (!$snippet->isOwnedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this snippet.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'code' => 'sometimes|required|string|max:1000000',
            'language_id' => 'nullable|uuid|exists:languages,id',
            'category_id' => 'nullable|uuid|exists:categories,id',
            'visibility' => ['sometimes', Rule::in(['public', 'private', 'team', 'unlisted'])],
            'file_name' => 'nullable|string|max:255',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|max:50',
            'expires_at' => 'nullable|date|after:now',
            'team_id' => 'nullable|uuid|exists:teams,id',
            'is_pinned' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If changing to team visibility, validate team membership
        if ($request->has('visibility') && $request->visibility === 'team') {
            $teamId = $request->team_id ?? $snippet->team_id;
            if (!$teamId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Team ID is required for team visibility.',
                ], 422);
            }

            $isMember = $user->teams()->where('teams.id', $teamId)->exists();
            if (!$isMember) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a member of this team.',
                ], 403);
            }
        }

        DB::beginTransaction();

        try {
            $oldLanguageId = $snippet->language_id;
            $oldCategoryId = $snippet->category_id;

            // Update snippet fields
            $updateData = [];
            $fillableFields = ['title', 'description', 'code', 'language_id', 'category_id',
                              'visibility', 'file_name', 'expires_at', 'is_pinned'];

            foreach ($fillableFields as $field) {
                if ($request->has($field)) {
                    $updateData[$field] = $request->$field;
                }
            }

            // Handle team_id based on visibility
            if ($request->has('visibility')) {
                $updateData['team_id'] = $request->visibility === 'team' ? ($request->team_id ?? $snippet->team_id) : null;
            }

            // Increment version if code changed
            if ($request->has('code') && $request->code !== $snippet->code) {
                $updateData['version'] = $snippet->version + 1;
            }

            $snippet->update($updateData);

            // Handle tags
            if ($request->has('tags')) {
                // Decrement old tags
                foreach ($snippet->tags as $oldTag) {
                    $oldTag->decrementUsageCount();
                }

                // Add new tags
                $tagIds = [];
                if (is_array($request->tags)) {
                    foreach ($request->tags as $tagName) {
                        $tagName = trim($tagName);
                        if (empty($tagName)) continue;

                        $tag = Tag::firstOrCreate(
                            ['name' => strtolower($tagName)],
                            ['name' => strtolower($tagName)]
                        );
                        $tag->incrementUsageCount();
                        $tagIds[] = $tag->id;
                    }
                }
                $snippet->tags()->sync($tagIds);
            }

            // Update language snippet counts
            if ($request->has('language_id') && $oldLanguageId !== $request->language_id) {
                if ($oldLanguageId) {
                    \App\Models\Language::where('id', $oldLanguageId)->decrement('snippet_count');
                }
                if ($request->language_id) {
                    \App\Models\Language::where('id', $request->language_id)->increment('snippet_count');
                }
            }

            // Update category snippet counts
            if ($request->has('category_id') && $oldCategoryId !== $request->category_id) {
                if ($oldCategoryId) {
                    \App\Models\Category::where('id', $oldCategoryId)->decrement('snippet_count');
                }
                if ($request->category_id) {
                    \App\Models\Category::where('id', $request->category_id)->increment('snippet_count');
                }
            }

            DB::commit();

            // Reload relationships
            $snippet->load(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url']);

            return response()->json([
                'success' => true,
                'message' => 'Snippet updated successfully.',
                'data' => $snippet,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update snippet.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Delete a snippet
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check ownership
        if (!$snippet->isOwnedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this snippet.',
            ], 403);
        }

        DB::beginTransaction();

        try {
            // Decrement tag usage counts
            foreach ($snippet->tags as $tag) {
                $tag->decrementUsageCount();
            }

            // Update user's snippet count
            $user->decrement('snippets_count');

            // Update language snippet count
            if ($snippet->language_id) {
                $snippet->language->decrement('snippet_count');
            }

            // Update category snippet count
            if ($snippet->category_id) {
                $snippet->category->decrement('snippet_count');
            }

            // Soft delete the snippet
            $snippet->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Snippet deleted successfully.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete snippet.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Toggle favorite status for a snippet
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function toggleFavorite(Request $request, string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check if user can view this snippet
        if (!$snippet->canBeViewedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to favorite this snippet.',
            ], 403);
        }

        $isFavorited = $snippet->favoritedBy()->where('user_id', $user->id)->exists();

        if ($isFavorited) {
            // Unfavorite
            $snippet->favoritedBy()->detach($user->id);
            $snippet->decrement('favorites_count');
            $message = 'Snippet removed from favorites.';
        } else {
            // Favorite
            $snippet->favoritedBy()->attach($user->id);
            $snippet->increment('favorites_count');
            $message = 'Snippet added to favorites.';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'is_favorited' => !$isFavorited,
                'favorites_count' => $snippet->fresh()->favorites_count,
            ],
        ]);
    }

    /**
     * Fork a snippet
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function fork(Request $request, string $id): JsonResponse
    {
        $originalSnippet = Snippet::find($id);

        if (!$originalSnippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check if user can view this snippet
        if (!$originalSnippet->canBeViewedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to fork this snippet.',
            ], 403);
        }

        // Cannot fork own snippet
        if ($originalSnippet->isOwnedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot fork your own snippet.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // Create forked snippet
            $forkedSnippet = Snippet::create([
                'user_id' => $user->id,
                'title' => $originalSnippet->title . ' (Fork)',
                'description' => $originalSnippet->description,
                'code' => $originalSnippet->code,
                'language_id' => $originalSnippet->language_id,
                'category_id' => $originalSnippet->category_id,
                'visibility' => 'private', // Forks start as private
                'file_name' => $originalSnippet->file_name,
                'forked_from_id' => $originalSnippet->id,
                'version' => 1,
            ]);

            // Copy tags
            $forkedSnippet->tags()->sync($originalSnippet->tags->pluck('id'));
            foreach ($originalSnippet->tags as $tag) {
                $tag->incrementUsageCount();
            }

            // Increment original snippet's fork count
            $originalSnippet->increment('forks_count');

            // Update user's snippet count
            $user->increment('snippets_count');

            // Update language snippet count
            if ($forkedSnippet->language_id) {
                $forkedSnippet->language->increment('snippet_count');
            }

            // Update category snippet count
            if ($forkedSnippet->category_id) {
                $forkedSnippet->category->increment('snippet_count');
            }

            DB::commit();

            // Load relationships
            $forkedSnippet->load(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url', 'forkedFrom:id,title,slug']);

            return response()->json([
                'success' => true,
                'message' => 'Snippet forked successfully.',
                'data' => $forkedSnippet,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to fork snippet.',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred.',
            ], 500);
        }
    }

    /**
     * Get user's favorited snippets
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function favorites(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = $user->favorites()
            ->notExpired()
            ->with(['language', 'category', 'tags', 'user:id,username,full_name,avatar_url']);

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['created_at', 'title', 'views_count', 'favorites_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        }

        // Paginate
        $perPage = min($request->get('per_page', 15), 100);
        $snippets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Favorite snippets retrieved successfully.',
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
     * Get forks of a snippet
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function forks(Request $request, string $id): JsonResponse
    {
        $snippet = Snippet::find($id);

        if (!$snippet) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet not found.',
            ], 404);
        }

        $user = $request->user();

        // Check if user can view this snippet
        if (!$snippet->canBeViewedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this snippet.',
            ], 403);
        }

        $forks = $snippet->forks()
            ->public()
            ->notExpired()
            ->with(['user:id,username,full_name,avatar_url'])
            ->orderByDesc('created_at')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'message' => 'Forks retrieved successfully.',
            'data' => $forks->items(),
            'meta' => [
                'current_page' => $forks->currentPage(),
                'last_page' => $forks->lastPage(),
                'per_page' => $forks->perPage(),
                'total' => $forks->total(),
            ],
        ]);
    }
}
