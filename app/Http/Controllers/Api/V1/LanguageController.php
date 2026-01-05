<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LanguageController extends Controller
{
    /**
     * Get all active languages
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Language::query();

        // Filter by active status (default: only active)
        if ($request->has('all') && $request->boolean('all')) {
            // Show all including inactive
        } else {
            $query->active();
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'name');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSorts = ['name', 'display_name', 'snippet_count', 'popularity_rank', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Get all or paginate
        if ($request->has('per_page')) {
            $perPage = min($request->get('per_page', 50), 100);
            $languages = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Languages retrieved successfully.',
                'data' => $languages->items(),
                'meta' => [
                    'current_page' => $languages->currentPage(),
                    'last_page' => $languages->lastPage(),
                    'per_page' => $languages->perPage(),
                    'total' => $languages->total(),
                ],
            ]);
        }

        $languages = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Languages retrieved successfully.',
            'data' => $languages,
        ]);
    }

    /**
     * Get popular languages
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function popular(Request $request): JsonResponse
    {
        $limit = min($request->get('limit', 20), 50);

        $languages = Language::active()
            ->orderByDesc('snippet_count')
            ->orderByDesc('popularity_rank')
            ->limit($limit)
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Popular languages retrieved successfully.',
            'data' => $languages,
        ]);
    }

    /**
     * Get a specific language by slug
     *
     * @param string $slug
     * @return JsonResponse
     */
    public function show(string $slug): JsonResponse
    {
        $language = Language::where('slug', $slug)->first();

        if (!$language) {
            return response()->json([
                'success' => false,
                'message' => 'Language not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Language retrieved successfully.',
            'data' => $language,
        ]);
    }
}
