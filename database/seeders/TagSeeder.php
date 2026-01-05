<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'api', 'description' => 'API related snippets', 'color' => '#3b82f6'],
            ['name' => 'authentication', 'description' => 'Authentication and authorization', 'color' => '#ef4444'],
            ['name' => 'database', 'description' => 'Database queries and operations', 'color' => '#f59e0b'],
            ['name' => 'frontend', 'description' => 'Frontend development', 'color' => '#10b981'],
            ['name' => 'backend', 'description' => 'Backend development', 'color' => '#8b5cf6'],
            ['name' => 'tutorial', 'description' => 'Tutorial and learning snippets', 'color' => '#ec4899'],
            ['name' => 'utility', 'description' => 'Utility functions and helpers', 'color' => '#6366f1'],
            ['name' => 'algorithm', 'description' => 'Algorithm implementations', 'color' => '#14b8a6'],
            ['name' => 'data-structure', 'description' => 'Data structure implementations', 'color' => '#f97316'],
            ['name' => 'testing', 'description' => 'Testing and test utilities', 'color' => '#84cc16'],
            ['name' => 'security', 'description' => 'Security related code', 'color' => '#dc2626'],
            ['name' => 'performance', 'description' => 'Performance optimization', 'color' => '#0891b2'],
            ['name' => 'react', 'description' => 'React.js snippets', 'color' => '#61dafb'],
            ['name' => 'vue', 'description' => 'Vue.js snippets', 'color' => '#42b883'],
            ['name' => 'laravel', 'description' => 'Laravel framework', 'color' => '#ff2d20'],
            ['name' => 'nodejs', 'description' => 'Node.js snippets', 'color' => '#339933'],
            ['name' => 'docker', 'description' => 'Docker and containerization', 'color' => '#2496ed'],
            ['name' => 'css', 'description' => 'CSS styling snippets', 'color' => '#264de4'],
            ['name' => 'animation', 'description' => 'Animation code', 'color' => '#ff6b6b'],
            ['name' => 'regex', 'description' => 'Regular expressions', 'color' => '#4ecdc4'],
        ];

        foreach ($tags as $tag) {
            Tag::create([
                'name' => $tag['name'],
                'description' => $tag['description'],
                'color' => $tag['color'],
                'usage_count' => rand(0, 100),
            ]);
        }
    }
}
