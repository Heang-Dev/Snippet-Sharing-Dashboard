<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Algorithms',
                'description' => 'Sorting, searching, graph algorithms, and more',
                'icon' => 'algorithm',
                'color' => '#6366f1',
                'children' => [
                    ['name' => 'Sorting', 'description' => 'Sorting algorithms like quicksort, mergesort, etc.'],
                    ['name' => 'Searching', 'description' => 'Binary search, linear search, and more'],
                    ['name' => 'Graph Algorithms', 'description' => 'BFS, DFS, Dijkstra, and more'],
                    ['name' => 'Dynamic Programming', 'description' => 'DP solutions and patterns'],
                ],
            ],
            [
                'name' => 'Data Structures',
                'description' => 'Arrays, linked lists, trees, graphs, and more',
                'icon' => 'data-structure',
                'color' => '#8b5cf6',
                'children' => [
                    ['name' => 'Arrays & Lists', 'description' => 'Array and list implementations'],
                    ['name' => 'Trees', 'description' => 'Binary trees, BST, AVL, etc.'],
                    ['name' => 'Graphs', 'description' => 'Graph implementations and utilities'],
                    ['name' => 'Hash Tables', 'description' => 'Hash map implementations'],
                ],
            ],
            [
                'name' => 'Web Development',
                'description' => 'Frontend, backend, and full-stack snippets',
                'icon' => 'web',
                'color' => '#ec4899',
                'children' => [
                    ['name' => 'Frontend', 'description' => 'HTML, CSS, JavaScript snippets'],
                    ['name' => 'Backend', 'description' => 'Server-side code snippets'],
                    ['name' => 'APIs', 'description' => 'REST, GraphQL API examples'],
                    ['name' => 'Authentication', 'description' => 'Auth implementations'],
                ],
            ],
            [
                'name' => 'Database',
                'description' => 'SQL queries, ORM patterns, and database utilities',
                'icon' => 'database',
                'color' => '#f59e0b',
                'children' => [
                    ['name' => 'SQL Queries', 'description' => 'Common SQL queries'],
                    ['name' => 'Migrations', 'description' => 'Database migration scripts'],
                    ['name' => 'ORM', 'description' => 'ORM patterns and examples'],
                ],
            ],
            [
                'name' => 'DevOps',
                'description' => 'CI/CD, Docker, Kubernetes, and infrastructure',
                'icon' => 'devops',
                'color' => '#10b981',
                'children' => [
                    ['name' => 'Docker', 'description' => 'Dockerfiles and compose files'],
                    ['name' => 'Kubernetes', 'description' => 'K8s manifests and configs'],
                    ['name' => 'CI/CD', 'description' => 'Pipeline configurations'],
                    ['name' => 'Scripts', 'description' => 'Automation scripts'],
                ],
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'iOS, Android, and cross-platform development',
                'icon' => 'mobile',
                'color' => '#06b6d4',
                'children' => [
                    ['name' => 'Android', 'description' => 'Android/Java/Kotlin snippets'],
                    ['name' => 'iOS', 'description' => 'Swift/Objective-C snippets'],
                    ['name' => 'React Native', 'description' => 'React Native components'],
                    ['name' => 'Flutter', 'description' => 'Flutter/Dart widgets'],
                ],
            ],
            [
                'name' => 'Testing',
                'description' => 'Unit tests, integration tests, and testing utilities',
                'icon' => 'test',
                'color' => '#84cc16',
                'children' => [
                    ['name' => 'Unit Tests', 'description' => 'Unit testing examples'],
                    ['name' => 'Integration Tests', 'description' => 'Integration test patterns'],
                    ['name' => 'Mocking', 'description' => 'Mock objects and stubs'],
                ],
            ],
            [
                'name' => 'Security',
                'description' => 'Encryption, authentication, and security patterns',
                'icon' => 'security',
                'color' => '#ef4444',
                'children' => [
                    ['name' => 'Encryption', 'description' => 'Encryption/decryption examples'],
                    ['name' => 'Hashing', 'description' => 'Password hashing and more'],
                    ['name' => 'Input Validation', 'description' => 'Sanitization and validation'],
                ],
            ],
            [
                'name' => 'Utilities',
                'description' => 'Helper functions and utility code',
                'icon' => 'utility',
                'color' => '#64748b',
                'children' => [
                    ['name' => 'String Manipulation', 'description' => 'String utility functions'],
                    ['name' => 'Date & Time', 'description' => 'Date/time utilities'],
                    ['name' => 'File Operations', 'description' => 'File I/O utilities'],
                    ['name' => 'Regex Patterns', 'description' => 'Common regex patterns'],
                ],
            ],
            [
                'name' => 'Design Patterns',
                'description' => 'Common software design patterns',
                'icon' => 'pattern',
                'color' => '#a855f7',
                'children' => [
                    ['name' => 'Creational', 'description' => 'Factory, Singleton, Builder, etc.'],
                    ['name' => 'Structural', 'description' => 'Adapter, Decorator, Facade, etc.'],
                    ['name' => 'Behavioral', 'description' => 'Observer, Strategy, Command, etc.'],
                ],
            ],
        ];

        $order = 0;
        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);

            $category = Category::create([
                ...$categoryData,
                'slug' => Str::slug($categoryData['name']),
                'order' => $order++,
                'is_active' => true,
            ]);

            $childOrder = 0;
            foreach ($children as $childData) {
                Category::create([
                    ...$childData,
                    'slug' => Str::slug($childData['name']),
                    'parent_category_id' => $category->id,
                    'icon' => $categoryData['icon'],
                    'color' => $categoryData['color'],
                    'order' => $childOrder++,
                    'is_active' => true,
                ]);
            }
        }
    }
}
