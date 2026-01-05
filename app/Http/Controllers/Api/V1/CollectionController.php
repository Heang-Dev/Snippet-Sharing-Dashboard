<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use App\Models\Snippet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    /**
     * Get authenticated user's collections
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Collection::where('user_id', Auth::id())
            ->withCount('snippets');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by visibility
        if ($request->has('visibility') && in_array($request->visibility, ['public', 'private'])) {
            $query->where('visibility', $request->visibility);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'created_at', 'updated_at', 'snippets_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $collections = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Collections retrieved successfully.',
            'data' => $collections->items(),
            'meta' => [
                'current_page' => $collections->currentPage(),
                'last_page' => $collections->lastPage(),
                'per_page' => $collections->perPage(),
                'total' => $collections->total(),
            ],
        ]);
    }

    /**
     * Get public collections (browse)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function publicIndex(Request $request): JsonResponse
    {
        $query = Collection::public()
            ->with(['user:id,username,full_name,avatar_url'])
            ->withCount('snippets');

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by user
        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $allowedSorts = ['name', 'created_at', 'updated_at', 'snippets_count'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $collections = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Public collections retrieved successfully.',
            'data' => $collections->items(),
            'meta' => [
                'current_page' => $collections->currentPage(),
                'last_page' => $collections->lastPage(),
                'per_page' => $collections->perPage(),
                'total' => $collections->total(),
            ],
        ]);
    }

    /**
     * Create a new collection
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'required|in:public,private',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $collection = Collection::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'description' => $request->description,
            'visibility' => $request->visibility,
            'snippets_count' => 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Collection created successfully.',
            'data' => $collection,
        ], 201);
    }

    /**
     * Get a specific collection
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $collection = Collection::with(['user:id,username,full_name,avatar_url'])
            ->withCount('snippets')
            ->find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check visibility permissions
        $user = Auth::user();
        if (!$collection->isPublic() && (!$user || !$collection->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this collection.',
            ], 403);
        }

        // Include snippets if requested
        if ($request->has('with_snippets') && $request->boolean('with_snippets')) {
            $collection->load(['snippets' => function ($q) {
                $q->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug,display_name,color'])
                    ->select(['snippets.id', 'snippets.title', 'snippets.slug', 'snippets.description', 'snippets.user_id', 'snippets.language_id', 'snippets.visibility', 'snippets.view_count', 'snippets.created_at']);
            }]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Collection retrieved successfully.',
            'data' => $collection,
        ]);
    }

    /**
     * Update a collection
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check ownership
        if (!$collection->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to update this collection.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'visibility' => 'sometimes|required|in:public,private',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $collection->update($request->only(['name', 'description', 'visibility']));

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully.',
            'data' => $collection->fresh(),
        ]);
    }

    /**
     * Delete a collection
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check ownership
        if (!$collection->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to delete this collection.',
            ], 403);
        }

        // Detach all snippets first
        $collection->snippets()->detach();

        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted successfully.',
        ]);
    }

    /**
     * Add a snippet to a collection
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function addSnippet(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check ownership
        if (!$collection->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this collection.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'snippet_id' => 'required|uuid|exists:snippets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $snippet = Snippet::find($request->snippet_id);

        // Check if snippet is accessible (public or owned by user)
        if (!$snippet->isPublic() && !$snippet->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot add this snippet to your collection.',
            ], 403);
        }

        // Check if already in collection
        if ($collection->snippets()->where('snippet_id', $snippet->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet is already in this collection.',
            ], 422);
        }

        // Get max sort order
        $maxOrder = $collection->snippets()->max('sort_order') ?? 0;

        // Add snippet to collection
        $collection->snippets()->attach($snippet->id, ['sort_order' => $maxOrder + 1]);

        // Update snippets count
        $collection->update(['snippets_count' => $collection->snippets()->count()]);

        return response()->json([
            'success' => true,
            'message' => 'Snippet added to collection successfully.',
            'data' => $collection->fresh()->load('snippets'),
        ]);
    }

    /**
     * Remove a snippet from a collection
     *
     * @param string $id
     * @param string $snippetId
     * @return JsonResponse
     */
    public function removeSnippet(string $id, string $snippetId): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check ownership
        if (!$collection->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this collection.',
            ], 403);
        }

        // Check if snippet is in collection
        if (!$collection->snippets()->where('snippet_id', $snippetId)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Snippet is not in this collection.',
            ], 404);
        }

        // Remove snippet from collection
        $collection->snippets()->detach($snippetId);

        // Update snippets count
        $collection->update(['snippets_count' => $collection->snippets()->count()]);

        return response()->json([
            'success' => true,
            'message' => 'Snippet removed from collection successfully.',
        ]);
    }

    /**
     * Reorder snippets in a collection
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function reorderSnippets(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check ownership
        if (!$collection->isOwnedBy(Auth::user())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to modify this collection.',
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'snippet_ids' => 'required|array',
            'snippet_ids.*' => 'uuid|exists:snippets,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Update sort order for each snippet
        foreach ($request->snippet_ids as $index => $snippetId) {
            $collection->snippets()->updateExistingPivot($snippetId, ['sort_order' => $index + 1]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Snippets reordered successfully.',
            'data' => $collection->fresh()->load('snippets'),
        ]);
    }

    /**
     * Get snippets in a collection
     *
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function snippets(Request $request, string $id): JsonResponse
    {
        $collection = Collection::find($id);

        if (!$collection) {
            return response()->json([
                'success' => false,
                'message' => 'Collection not found.',
            ], 404);
        }

        // Check visibility permissions
        $user = Auth::user();
        if (!$collection->isPublic() && (!$user || !$collection->isOwnedBy($user))) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to view this collection.',
            ], 403);
        }

        $query = $collection->snippets()
            ->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug,display_name,color', 'tags:id,name,slug,color']);

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $snippets = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Collection snippets retrieved successfully.',
            'data' => $snippets->items(),
            'meta' => [
                'current_page' => $snippets->currentPage(),
                'last_page' => $snippets->lastPage(),
                'per_page' => $snippets->perPage(),
                'total' => $snippets->total(),
            ],
        ]);
    }
}
