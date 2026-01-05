<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::where('privacy', 'public')->get();

        if ($users->isEmpty() || $snippets->isEmpty()) {
            $this->command->warn('Please run UserSeeder and SnippetSeeder first.');
            return;
        }

        $comments = [
            'Great snippet! This really helped me understand the concept.',
            'Thanks for sharing! I had been looking for something like this.',
            'Nice implementation. Have you considered using recursion here?',
            'This is exactly what I needed for my project!',
            'Clean and readable code. Well done!',
            'I found a small optimization - you could use array_map instead of foreach.',
            'Would this work with TypeScript as well?',
            'Excellent explanation in the comments. Very helpful!',
            'I adapted this for my use case. Works perfectly!',
            'Could you add error handling to make it more robust?',
            'This is a great starting point for beginners.',
            'I learned something new today. Thanks!',
            'How would you test this function?',
            'Performance looks good. Did you benchmark it?',
            'Simple and elegant solution.',
        ];

        $replies = [
            'Great question! Yes, it would work with TypeScript.',
            'Thanks for the feedback!',
            'Good point, I\'ll update the snippet.',
            'Glad it helped!',
            'I\'ll add that in the next version.',
        ];

        foreach ($snippets as $snippet) {
            // Add 1-4 root comments per snippet
            $numComments = rand(1, 4);

            for ($i = 0; $i < $numComments; $i++) {
                $comment = Comment::create([
                    'snippet_id' => $snippet->id,
                    'user_id' => $users->random()->id,
                    'content' => $comments[array_rand($comments)],
                    'is_edited' => rand(0, 10) > 8, // 20% chance of being edited
                ]);

                // 50% chance of having replies
                if (rand(0, 1) === 1) {
                    $numReplies = rand(1, 2);

                    for ($j = 0; $j < $numReplies; $j++) {
                        Comment::create([
                            'snippet_id' => $snippet->id,
                            'user_id' => $users->random()->id,
                            'parent_comment_id' => $comment->id,
                            'content' => $replies[array_rand($replies)],
                            'is_edited' => false,
                        ]);
                    }
                }
            }

            // Update comment count on snippet
            $snippet->update([
                'comment_count' => $snippet->allComments()->count(),
            ]);
        }
    }
}
