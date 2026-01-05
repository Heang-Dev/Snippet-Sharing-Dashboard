<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Language;
use App\Models\Snippet;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    /**
     * Global search across all resources
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('q', $request->get('query', ''));
        $type = $request->get('type', 'all');
        $limit = min($request->get('limit', 10), 50);

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters.',
            ], 422);
        }

        $results = [];

        // Search snippets
        if ($type === 'all' || $type === 'snippets') {
            $results['snippets'] = $this->searchSnippets($query, $limit);
        }

        // Search users
        if ($type === 'all' || $type === 'users') {
            $results['users'] = $this->searchUsers($query, $limit);
        }

        // Search collections
        if ($type === 'all' || $type === 'collections') {
            $results['collections'] = $this->searchCollections($query, $limit);
        }

        // Search tags
        if ($type === 'all' || $type === 'tags') {
            $results['tags'] = $this->searchTags($query, $limit);
        }

        // Search languages
        if ($type === 'all' || $type === 'languages') {
            $results['languages'] = $this->searchLanguages($query, $limit);
        }

        // Search categories
        if ($type === 'all' || $type === 'categories') {
            $results['categories'] = $this->searchCategories($query, $limit);
        }

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully.',
            'data' => $results,
            'meta' => [
                'query' => $query,
                'type' => $type,
            ],
        ]);
    }

    /**
     * Search snippets with advanced filters
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function snippets(Request $request): JsonResponse
    {
        $query = $request->get('q', $request->get('query', ''));

        $snippetQuery = Snippet::query()
            ->with([
                'user:id,username,full_name,avatar_url',
                'language:id,name,slug,display_name,color',
                'tags:id,name,slug,color',
            ])
            ->where('visibility', 'public');

        // Text search
        if (!empty($query)) {
            $snippetQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('code', 'like', "%{$query}%");
            });
        }

        // Filter by language
        if ($request->has('language_id')) {
            $snippetQuery->where('language_id', $request->language_id);
        }

        if ($request->has('language')) {
            $language = Language::where('slug', $request->language)->first();
            if ($language) {
                $snippetQuery->where('language_id', $language->id);
            }
        }

        // Filter by category
        if ($request->has('category_id')) {
            $snippetQuery->where('category_id', $request->category_id);
        }

        if ($request->has('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $snippetQuery->where('category_id', $category->id);
            }
        }

        // Filter by tags
        if ($request->has('tags')) {
            $tagSlugs = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $snippetQuery->whereHas('tags', function ($q) use ($tagSlugs) {
                $q->whereIn('slug', $tagSlugs);
            });
        }

        // Filter by user
        if ($request->has('user_id')) {
            $snippetQuery->where('user_id', $request->user_id);
        }

        if ($request->has('username')) {
            $user = User::where('username', $request->username)->first();
            if ($user) {
                $snippetQuery->where('user_id', $user->id);
            }
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $snippetQuery->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->has('to_date')) {
            $snippetQuery->whereDate('created_at', '<=', $request->to_date);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'relevance');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'created_at':
            case 'updated_at':
            case 'view_count':
            case 'favorite_count':
                $snippetQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
                break;
            case 'relevance':
            default:
                // For relevance, prioritize title matches, then description, then code
                if (!empty($query)) {
                    $snippetQuery->orderByRaw("
                        CASE
                            WHEN title LIKE ? THEN 1
                            WHEN title LIKE ? THEN 2
                            WHEN description LIKE ? THEN 3
                            ELSE 4
                        END
                    ", ["{$query}%", "%{$query}%", "%{$query}%"]);
                }
                $snippetQuery->orderBy('view_count', 'desc');
                break;
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $snippets = $snippetQuery->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully.',
            'data' => $snippets->items(),
            'meta' => [
                'query' => $query,
                'current_page' => $snippets->currentPage(),
                'last_page' => $snippets->lastPage(),
                'per_page' => $snippets->perPage(),
                'total' => $snippets->total(),
            ],
        ]);
    }

    /**
     * Search users
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function users(Request $request): JsonResponse
    {
        $query = $request->get('q', $request->get('query', ''));

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Search query must be at least 2 characters.',
            ], 422);
        }

        $userQuery = User::query()
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('full_name', 'like', "%{$query}%")
                    ->orWhere('bio', 'like', "%{$query}%");
            })
            ->withCount(['snippets' => function ($q) {
                $q->where('visibility', 'public');
            }])
            ->select(['id', 'username', 'full_name', 'bio', 'avatar_url', 'created_at']);

        // Sort
        $sortBy = $request->get('sort_by', 'relevance');
        $sortOrder = $request->get('sort_order', 'desc');

        switch ($sortBy) {
            case 'created_at':
            case 'snippets_count':
                $userQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
                break;
            case 'relevance':
            default:
                $userQuery->orderByRaw("
                    CASE
                        WHEN username LIKE ? THEN 1
                        WHEN username LIKE ? THEN 2
                        WHEN full_name LIKE ? THEN 3
                        ELSE 4
                    END
                ", ["{$query}%", "%{$query}%", "%{$query}%"]);
                break;
        }

        // Pagination
        $perPage = min($request->get('per_page', 20), 100);
        $users = $userQuery->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Search results retrieved successfully.',
            'data' => $users->items(),
            'meta' => [
                'query' => $query,
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Autocomplete/suggestions for search
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->get('q', $request->get('query', ''));
        $limit = min($request->get('limit', 5), 20);

        if (empty($query)) {
            return response()->json([
                'success' => true,
                'message' => 'Suggestions retrieved successfully.',
                'data' => [],
            ]);
        }

        $suggestions = [];

        // Snippet titles
        $snippetTitles = Snippet::where('visibility', 'public')
            ->where('title', 'like', "{$query}%")
            ->limit($limit)
            ->pluck('title')
            ->map(function ($title) {
                return ['type' => 'snippet', 'value' => $title];
            });
        $suggestions = array_merge($suggestions, $snippetTitles->toArray());

        // Tags
        $tags = Tag::where('name', 'like', "{$query}%")
            ->orderByDesc('usage_count')
            ->limit($limit)
            ->pluck('name')
            ->map(function ($name) {
                return ['type' => 'tag', 'value' => $name];
            });
        $suggestions = array_merge($suggestions, $tags->toArray());

        // Languages
        $languages = Language::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "{$query}%")
                    ->orWhere('display_name', 'like', "{$query}%");
            })
            ->limit($limit)
            ->pluck('display_name')
            ->map(function ($name) {
                return ['type' => 'language', 'value' => $name];
            });
        $suggestions = array_merge($suggestions, $languages->toArray());

        // Users
        $users = User::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', "{$query}%")
                    ->orWhere('full_name', 'like', "{$query}%");
            })
            ->limit($limit)
            ->get(['username', 'full_name'])
            ->map(function ($user) {
                return ['type' => 'user', 'value' => $user->username, 'display' => $user->full_name];
            });
        $suggestions = array_merge($suggestions, $users->toArray());

        // Limit total suggestions
        $suggestions = array_slice($suggestions, 0, $limit * 2);

        return response()->json([
            'success' => true,
            'message' => 'Suggestions retrieved successfully.',
            'data' => $suggestions,
        ]);
    }

    /**
     * Search snippets helper
     */
    private function searchSnippets(string $query, int $limit): array
    {
        return Snippet::where('visibility', 'public')
            ->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['user:id,username,full_name,avatar_url', 'language:id,name,slug,display_name,color'])
            ->orderByRaw("CASE WHEN title LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->orderBy('view_count', 'desc')
            ->limit($limit)
            ->get(['id', 'title', 'slug', 'description', 'user_id', 'language_id', 'view_count', 'created_at'])
            ->toArray();
    }

    /**
     * Search users helper
     */
    private function searchUsers(string $query, int $limit): array
    {
        return User::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('username', 'like', "%{$query}%")
                    ->orWhere('full_name', 'like', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN username LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->limit($limit)
            ->get(['id', 'username', 'full_name', 'avatar_url'])
            ->toArray();
    }

    /**
     * Search collections helper
     */
    private function searchCollections(string $query, int $limit): array
    {
        return Collection::where('visibility', 'public')
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->with(['user:id,username,full_name,avatar_url'])
            ->withCount('snippets')
            ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description', 'user_id', 'snippets_count', 'created_at'])
            ->toArray();
    }

    /**
     * Search tags helper
     */
    private function searchTags(string $query, int $limit): array
    {
        return Tag::where('name', 'like', "%{$query}%")
            ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->orderBy('usage_count', 'desc')
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'color', 'usage_count'])
            ->toArray();
    }

    /**
     * Search languages helper
     */
    private function searchLanguages(string $query, int $limit): array
    {
        return Language::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('display_name', 'like', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'display_name', 'color', 'snippet_count'])
            ->toArray();
    }

    /**
     * Search categories helper
     */
    private function searchCategories(string $query, int $limit): array
    {
        return Category::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            })
            ->orderByRaw("CASE WHEN name LIKE ? THEN 1 ELSE 2 END", ["{$query}%"])
            ->limit($limit)
            ->get(['id', 'name', 'slug', 'description', 'color', 'snippet_count'])
            ->toArray();
    }
}
