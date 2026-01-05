<?php

namespace Database\Seeders;

use App\Models\Collection;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Seeder;

class CollectionSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::all();

        if ($users->isEmpty() || $snippets->isEmpty()) {
            $this->command->warn('Please run UserSeeder and SnippetSeeder first.');
            return;
        }

        $collections = [
            [
                'name' => 'JavaScript Essentials',
                'description' => 'Must-know JavaScript snippets for everyday development',
                'privacy' => 'public',
            ],
            [
                'name' => 'Algorithm Patterns',
                'description' => 'Common algorithm implementations and patterns',
                'privacy' => 'public',
            ],
            [
                'name' => 'Laravel Best Practices',
                'description' => 'Collection of Laravel code snippets following best practices',
                'privacy' => 'public',
            ],
            [
                'name' => 'My Learning Journey',
                'description' => 'Personal collection of snippets while learning new technologies',
                'privacy' => 'private',
            ],
            [
                'name' => 'Interview Prep',
                'description' => 'Code snippets useful for technical interviews',
                'privacy' => 'public',
            ],
            [
                'name' => 'DevOps Toolkit',
                'description' => 'Docker, CI/CD, and infrastructure snippets',
                'privacy' => 'public',
            ],
            [
                'name' => 'CSS Tricks',
                'description' => 'Useful CSS patterns and animations',
                'privacy' => 'public',
            ],
            [
                'name' => 'API Development',
                'description' => 'RESTful API patterns and implementations',
                'privacy' => 'public',
            ],
        ];

        foreach ($collections as $collectionData) {
            $user = $users->random();

            $collection = Collection::create([
                'user_id' => $user->id,
                'name' => $collectionData['name'],
                'description' => $collectionData['description'],
                'privacy' => $collectionData['privacy'],
            ]);

            // Add random snippets to the collection
            $randomSnippets = $snippets->random(min(rand(3, 6), $snippets->count()));
            $order = 0;

            foreach ($randomSnippets as $snippet) {
                $collection->snippets()->attach($snippet->id, ['position' => $order++]);
            }

            // Update snippet count
            $collection->update(['snippet_count' => $collection->snippets()->count()]);
        }
    }
}
