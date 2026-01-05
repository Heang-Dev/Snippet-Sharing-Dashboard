<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TagController extends Controller
{
    /**
     * Get all tags
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Tag::query();

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSorts = ['name', 'usage_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Get all or paginate
        if ($request->has('per_page')) {
            $perPage = min($request->get('per_page', 50), 100);
            $tags = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Tags retrieved successfully.',
                'data' => $tags->items(),
                'meta' => [
                    'current_page' => $tags->currentPage(),
                    'last_page' => $tags->lastPage(),
                    'per_page' => $tags->perPage(),
                    'total' => $tags->total(),
                ],
            ]);
        }

        $tags = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Tags retrieved successfully.',
            'data' => $tags,
        ]);
    }

    /**
     * Get popular tags
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 20), 100);

        $tags = Tag::popular($limit)->get();

        return response()->json([
            'success' => true,
            'message' => 'Popular tags retrieved successfully.',
            'data' => $tags,
        ]);
    }

    /**
     * Search/autocomplete tags
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->get('q', $request->get('search', ''));
        $limit = min($request->get('limit', 10), 50);

        if (empty($search)) {
            return response()->json([
                'success' => true,
                'message' => 'Tags retrieved successfully.',
                'data' => [],
            ]);
        }

        $tags = Tag::where('name', 'like', "{$search}%")
            ->orWhere('name', 'like', "%{$search}%")
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'usage_count']);

        return response()->json([
            'success' => true,
            'message' => 'Tags retrieved successfully.',
            'data' => $tags,
        ]);
    }

    /**
     * Get a specific tag by slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::where('slug', $slug)->first();

        if (!$tag) {
            return response()->json([
                'success' => false,
                'message' => 'Tag not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Tag retrieved successfully.',
            'data' => $tag,
        ]);
    }
}
