<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class FollowSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 3) {
            $this->command->warn('Please run UserSeeder first with at least 3 users.');
            return;
        }

        // Create follow relationships
        foreach ($users as $user) {
            // Each user follows 2-5 random other users
            $potentialFollowing = $users->where('id', '!=', $user->id);
            $numToFollow = min(rand(2, 5), $potentialFollowing->count());
            $toFollow = $potentialFollowing->random($numToFollow);

            foreach ($toFollow as $followee) {
                // Check if not already following
                if (!$user->following()->where('following_id', $followee->id)->exists()) {
                    $user->following()->attach($followee->id);
                }
            }
        }

        // Update follower/following counts
        foreach ($users as $user) {
            $user->update([
                'followers_count' => $user->followers()->count(),
                'following_count' => $user->following()->count(),
            ]);
        }
    }
}
