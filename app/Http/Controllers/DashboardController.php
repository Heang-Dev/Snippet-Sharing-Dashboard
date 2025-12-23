<?php

namespace App\Http\Controllers;

use App\Models\Snippet;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = Auth::user();

        $stats = [
            'total_snippets' => Snippet::where('user_id', $user->id)->count(),
            'public_snippets' => Snippet::where('user_id', $user->id)->where('visibility', 'public')->count(),
            'private_snippets' => Snippet::where('user_id', $user->id)->where('visibility', 'private')->count(),
            'total_views' => Snippet::where('user_id', $user->id)->sum('views_count'),
            'total_favorites' => Snippet::where('user_id', $user->id)->sum('favorites_count'),
            'followers_count' => $user->followers()->count(),
            'following_count' => $user->following()->count(),
        ];

        $recentSnippets = Snippet::where('user_id', $user->id)
            ->with(['language', 'category'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $popularSnippets = Snippet::where('user_id', $user->id)
            ->with(['language', 'category'])
            ->orderByDesc('views_count')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentSnippets' => $recentSnippets,
            'popularSnippets' => $popularSnippets,
        ]);
    }
}
