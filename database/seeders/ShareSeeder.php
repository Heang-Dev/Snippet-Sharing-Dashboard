<?php

namespace Database\Seeders;

use App\Models\Share;
use App\Models\Snippet;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShareSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::all();
        $teams = Team::all();

        if ($users->isEmpty() || $snippets->isEmpty()) {
            $this->command->warn('Please run UserSeeder and SnippetSeeder first.');
            return;
        }

        // Create various types of shares
        foreach ($snippets->take(8) as $snippet) {
            $owner = $users->where('id', $snippet->user_id)->first() ?? $users->first();
            $otherUsers = $users->where('id', '!=', $owner->id);

            // Link share (public link)
            Share::create([
                'snippet_id' => $snippet->id,
                'shared_by' => $owner->id,
                'share_type' => 'link',
                'share_token' => Str::random(64),
                'permission' => 'view',
                'expires_at' => rand(0, 1) ? now()->addDays(30) : null,
                'access_count' => rand(0, 50),
                'is_active' => true,
            ]);

            // User share (direct share with another user)
            if ($otherUsers->isNotEmpty()) {
                $recipient = $otherUsers->random();
                Share::create([
                    'snippet_id' => $snippet->id,
                    'shared_by' => $owner->id,
                    'shared_with' => $recipient->id,
                    'share_type' => 'user',
                    'permission' => rand(0, 1) ? 'view' : 'edit',
                    'access_count' => rand(0, 10),
                    'is_active' => true,
                ]);
            }

            // Team share
            if ($teams->isNotEmpty() && rand(0, 1)) {
                $team = $teams->random();
                Share::create([
                    'snippet_id' => $snippet->id,
                    'shared_by' => $owner->id,
                    'team_id' => $team->id,
                    'share_type' => 'team',
                    'permission' => 'view',
                    'access_count' => rand(0, 20),
                    'is_active' => true,
                ]);
            }

            // Email share
            if (rand(0, 1)) {
                Share::create([
                    'snippet_id' => $snippet->id,
                    'shared_by' => $owner->id,
                    'share_type' => 'email',
                    'share_token' => Str::random(64),
                    'permission' => 'view',
                    'email' => 'external' . rand(1, 100) . '@example.com',
                    'expires_at' => now()->addDays(7),
                    'access_count' => rand(0, 5),
                    'is_active' => true,
                ]);
            }
        }

        // Update share counts on snippets
        foreach ($snippets as $snippet) {
            $snippet->update([
                'share_count' => $snippet->shares()->count(),
            ]);
        }
    }
}
