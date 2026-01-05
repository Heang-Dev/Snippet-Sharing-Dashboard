<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Get all active categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = Category::query();

        // Filter by active status (default: only active)
        if ($request->has('all') && $request->boolean('all')) {
            // Show all including inactive
        } else {
            $query->active();
        }

        // Filter by parent (roots only or specific parent)
        if ($request->has('roots') && $request->boolean('roots')) {
            $query->roots();
        } elseif ($request->has('parent_id')) {
            if ($request->parent_id === 'null' || $request->parent_id === '') {
                $query->roots();
            } else {
                $query->where('parent_category_id', $request->parent_id);
            }
        }

        // Include children
        if ($request->has('with_children') && $request->boolean('with_children')) {
            $query->with(['children' => function ($q) {
                $q->active()->orderBy('order')->orderBy('name');
            }]);
        }

        // Search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'order');
        $sortOrder = $request->get('sort_order', 'asc');
        $allowedSorts = ['name', 'order', 'snippet_count', 'created_at'];

        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder === 'desc' ? 'desc' : 'asc');
        }

        // Secondary sort by name
        if ($sortBy !== 'name') {
            $query->orderBy('name', 'asc');
        }

        // Get all or paginate
        if ($request->has('per_page')) {
            $perPage = min($request->get('per_page', 50), 100);
            $categories = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully.',
                'data' => $categories->items(),
                'meta' => [
                    'current_page' => $categories->currentPage(),
                    'last_page' => $categories->lastPage(),
                    'per_page' => $categories->perPage(),
                    'total' => $categories->total(),
                ],
            ]);
        }

        $categories = $query->get();

        return response()->json([
            'success' => true,
            'message' => 'Categories retrieved successfully.',
            'data' => $categories,
        ]);
    }

    /**
     * Get category tree (hierarchical)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function tree(Request $request): JsonResponse
    {
        $categories = Category::active()
            ->roots()
            ->with(['children' => function ($q) {
                $q->active()->orderBy('order')->orderBy('name');
            }])
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Category tree retrieved successfully.',
            'data' => $categories,
        ]);
    }

    /**
     * Get a specific category by slug
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(Request $request, string $slug): JsonResponse
    {
        $query = Category::where('slug', $slug);

        // Include children
        if ($request->has('with_children') && $request->boolean('with_children')) {
            $query->with(['children' => function ($q) {
                $q->active()->orderBy('order')->orderBy('name');
            }]);
        }

        // Include parent
        if ($request->has('with_parent') && $request->boolean('with_parent')) {
            $query->with('parent');
        }

        $category = $query->first();

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Category retrieved successfully.',
            'data' => $category,
        ]);
    }
}
