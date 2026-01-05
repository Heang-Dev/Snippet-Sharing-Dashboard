<?php

namespace Database\Seeders;

use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Seeder;

class FavoriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::where('privacy', 'public')->get();

        if ($users->isEmpty() || $snippets->isEmpty()) {
            $this->command->warn('Please run UserSeeder and SnippetSeeder first.');
            return;
        }

        foreach ($users as $user) {
            // Each user favorites 3-8 random public snippets
            $numFavorites = min(rand(3, 8), $snippets->count());
            $toFavorite = $snippets->random($numFavorites);

            foreach ($toFavorite as $snippet) {
                // Check if not already favorited
                if (!$user->favorites()->where('snippet_id', $snippet->id)->exists()) {
                    $user->favorites()->attach($snippet->id);
                }
            }
        }

        // Update favorite counts on snippets
        foreach ($snippets as $snippet) {
            $snippet->update([
                'favorite_count' => $snippet->favoritedBy()->count(),
            ]);
        }
    }
}
