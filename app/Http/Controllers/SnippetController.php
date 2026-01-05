<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Snippet;
use App\Models\SnippetVersion;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class SnippetController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Snippet::where('user_id', Auth::id())
            ->with(['language', 'category', 'tags']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('language')) {
            $query->where('language_id', $request->language);
        }

        if ($request->filled('privacy')) {
            $query->where('privacy', $request->privacy);
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $snippets = $query->paginate(15)->withQueryString();

        $languages = Language::active()->orderBy('name')->get();
        $categories = Category::active()->roots()->with('children')->orderBy('order')->get();

        return Inertia::render('Snippets/Index', [
            'snippets' => $snippets,
            'languages' => $languages,
            'categories' => $categories,
            'filters' => $request->only(['search', 'language', 'privacy', 'category', 'sort', 'direction']),
        ]);
    }

    public function create(): Response
    {
        $languages = Language::active()->orderBy('name')->get();
        $categories = Category::active()->roots()->with('children')->orderBy('order')->get();

        return Inertia::render('Snippets/Create', [
            'languages' => $languages,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'code' => ['required', 'string', 'max:100000'],
            'language_id' => ['required', 'uuid', 'exists:languages,id'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'privacy' => ['required', 'in:public,private,team'],
            'team_id' => ['nullable', 'uuid', 'exists:teams,id', 'required_if:privacy,team'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $snippet = Snippet::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'code' => $validated['code'],
            'language_id' => $validated['language_id'],
            'category_id' => $validated['category_id'] ?? null,
            'privacy' => $validated['privacy'],
            'team_id' => $validated['team_id'] ?? null,
            'file_name' => $validated['file_name'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'version' => 1,
        ]);

        // Create initial version
        SnippetVersion::create([
            'snippet_id' => $snippet->id,
            'user_id' => Auth::id(),
            'version_number' => 1,
            'code' => $validated['code'],
            'change_description' => 'Initial version',
        ]);

        // Handle tags
        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => trim($tagName)],
                    ['slug' => Str::slug(trim($tagName))]
                );
                $tag->incrementUsageCount();
                $tagIds[] = $tag->id;
            }
            $snippet->tags()->sync($tagIds);
        }

        return redirect()
            ->route('snippets.show', $snippet)
            ->with('success', 'Snippet created successfully!');
    }

    public function show(Snippet $snippet): Response
    {
        // Check permissions
        if (!$snippet->canBeViewedBy(Auth::user())) {
            abort(403, 'You do not have permission to view this snippet.');
        }

        $snippet->load(['user', 'language', 'category', 'tags', 'versions' => function ($query) {
            $query->orderByDesc('version_number')->limit(10);
        }, 'comments' => function ($query) {
            $query->with('user', 'replies.user')->whereNull('parent_id')->orderByDesc('created_at');
        }]);

        // Record view if not the owner
        if (!$snippet->isOwnedBy(Auth::user())) {
            $snippet->views()->create([
                'user_id' => Auth::id(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'referer' => request()->header('referer'),
            ]);
            $snippet->incrementViewCount();
        }

        $isFavorited = Auth::user() ? Auth::user()->hasFavorited($snippet) : false;

        return Inertia::render('Snippets/Show', [
            'snippet' => $snippet,
            'isFavorited' => $isFavorited,
        ]);
    }

    public function edit(Snippet $snippet): Response
    {
        if (!$snippet->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to edit this snippet.');
        }

        $snippet->load(['tags', 'language', 'category']);

        $languages = Language::active()->orderBy('name')->get();
        $categories = Category::active()->roots()->with('children')->orderBy('order')->get();

        return Inertia::render('Snippets/Edit', [
            'snippet' => $snippet,
            'languages' => $languages,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Snippet $snippet): RedirectResponse
    {
        if (!$snippet->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to edit this snippet.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'code' => ['required', 'string', 'max:100000'],
            'language_id' => ['required', 'uuid', 'exists:languages,id'],
            'category_id' => ['nullable', 'uuid', 'exists:categories,id'],
            'privacy' => ['required', 'in:public,private,team'],
            'team_id' => ['nullable', 'uuid', 'exists:teams,id', 'required_if:privacy,team'],
            'file_name' => ['nullable', 'string', 'max:255'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'expires_at' => ['nullable', 'date', 'after:now'],
            'change_description' => ['nullable', 'string', 'max:500'],
        ]);

        // Check if code changed - create new version
        $codeChanged = $snippet->code !== $validated['code'];
        if ($codeChanged) {
            $newVersion = $snippet->version + 1;
            SnippetVersion::create([
                'snippet_id' => $snippet->id,
                'user_id' => Auth::id(),
                'version_number' => $newVersion,
                'code' => $validated['code'],
                'change_description' => $validated['change_description'] ?? 'Updated code',
            ]);
            $validated['version'] = $newVersion;
        }

        $snippet->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'code' => $validated['code'],
            'language_id' => $validated['language_id'],
            'category_id' => $validated['category_id'] ?? null,
            'privacy' => $validated['privacy'],
            'team_id' => $validated['team_id'] ?? null,
            'file_name' => $validated['file_name'] ?? null,
            'expires_at' => $validated['expires_at'] ?? null,
            'version' => $validated['version'] ?? $snippet->version,
        ]);

        // Handle tags
        $oldTags = $snippet->tags;

        if (!empty($validated['tags'])) {
            $tagIds = [];
            foreach ($validated['tags'] as $tagName) {
                $tag = Tag::firstOrCreate(
                    ['name' => trim($tagName)],
                    ['slug' => Str::slug(trim($tagName))]
                );
                $tagIds[] = $tag->id;
            }

            // Decrement old tags
            foreach ($oldTags as $oldTag) {
                if (!in_array($oldTag->id, $tagIds)) {
                    $oldTag->decrementUsageCount();
                }
            }

            // Increment new tags
            foreach ($tagIds as $tagId) {
                if (!$oldTags->contains('id', $tagId)) {
                    Tag::find($tagId)?->incrementUsageCount();
                }
            }

            $snippet->tags()->sync($tagIds);
        } else {
            foreach ($oldTags as $oldTag) {
                $oldTag->decrementUsageCount();
            }
            $snippet->tags()->detach();
        }

        return redirect()
            ->route('snippets.show', $snippet)
            ->with('success', 'Snippet updated successfully!');
    }

    public function destroy(Snippet $snippet): RedirectResponse
    {
        if (!$snippet->isOwnedBy(Auth::user())) {
            abort(403, 'You do not have permission to delete this snippet.');
        }

        // Decrement tag usage counts
        foreach ($snippet->tags as $tag) {
            $tag->decrementUsageCount();
        }

        $snippet->delete();

        return redirect()
            ->route('snippets.index')
            ->with('success', 'Snippet deleted successfully!');
    }

    public function toggleFavorite(Snippet $snippet): RedirectResponse
    {
        $user = Auth::user();

        if ($user->hasFavorited($snippet)) {
            $user->favorites()->detach($snippet->id);
            $snippet->decrement('favorites_count');
            $message = 'Removed from favorites';
        } else {
            $user->favorites()->attach($snippet->id);
            $snippet->increment('favorites_count');
            $message = 'Added to favorites';
        }

        return back()->with('success', $message);
    }

    public function fork(Snippet $snippet): RedirectResponse
    {
        if (!$snippet->canBeViewedBy(Auth::user())) {
            abort(403, 'You do not have permission to fork this snippet.');
        }

        $forkedSnippet = Snippet::create([
            'user_id' => Auth::id(),
            'title' => $snippet->title . ' (Fork)',
            'description' => $snippet->description,
            'code' => $snippet->code,
            'language_id' => $snippet->language_id,
            'category_id' => $snippet->category_id,
            'privacy' => 'private', // Default to private for forks
            'forked_from_id' => $snippet->id,
            'version' => 1,
        ]);

        // Create initial version
        SnippetVersion::create([
            'snippet_id' => $forkedSnippet->id,
            'user_id' => Auth::id(),
            'version_number' => 1,
            'code' => $snippet->code,
            'change_description' => 'Forked from ' . $snippet->title,
        ]);

        // Copy tags
        $forkedSnippet->tags()->sync($snippet->tags->pluck('id'));
        foreach ($snippet->tags as $tag) {
            $tag->incrementUsageCount();
        }

        // Increment fork count on original
        $snippet->increment('forks_count');

        return redirect()
            ->route('snippets.edit', $forkedSnippet)
            ->with('success', 'Snippet forked successfully!');
    }
}
