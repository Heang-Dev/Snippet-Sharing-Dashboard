<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Seeder;

class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::all();

        if ($users->isEmpty()) {
            $this->command->warn('Please run UserSeeder first.');
            return;
        }

        $actions = [
            ['action' => 'create', 'resource_type' => 'snippet'],
            ['action' => 'update', 'resource_type' => 'snippet'],
            ['action' => 'delete', 'resource_type' => 'snippet'],
            ['action' => 'create', 'resource_type' => 'collection'],
            ['action' => 'update', 'resource_type' => 'user'],
            ['action' => 'login', 'resource_type' => 'auth'],
            ['action' => 'logout', 'resource_type' => 'auth'],
            ['action' => 'create', 'resource_type' => 'team'],
            ['action' => 'invite', 'resource_type' => 'team_member'],
            ['action' => 'share', 'resource_type' => 'snippet'],
        ];

        $methods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];
        $endpoints = [
            '/api/v1/snippets',
            '/api/v1/collections',
            '/api/v1/users/profile',
            '/api/v1/teams',
            '/api/v1/auth/login',
            '/api/v1/auth/logout',
        ];

        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36',
            'Mozilla/5.0 (Linux; Android 11; Pixel 5) AppleWebKit/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15',
            'SnippetApp/1.0 (Android)',
            'PostmanRuntime/7.29.0',
        ];

        // Create audit logs for the past 30 days
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $actionData = $actions[array_rand($actions)];
            $snippet = $snippets->isNotEmpty() && $actionData['resource_type'] === 'snippet'
                ? $snippets->random()
                : null;

            AuditLog::create([
                'user_id' => $user->id,
                'action' => $actionData['action'],
                'resource_type' => $actionData['resource_type'],
                'resource_id' => $snippet?->id,
                'old_values' => $actionData['action'] === 'update' ? ['title' => 'Old Title'] : null,
                'new_values' => $actionData['action'] === 'update' ? ['title' => 'New Title'] : null,
                'ip_address' => '192.168.' . rand(1, 255) . '.' . rand(1, 255),
                'user_agent' => $userAgents[array_rand($userAgents)],
                'method' => $methods[array_rand($methods)],
                'endpoint' => $endpoints[array_rand($endpoints)],
                'status_code' => rand(0, 10) > 1 ? 200 : (rand(0, 1) ? 400 : 500),
                'created_at' => now()->subDays(rand(0, 30))->subHours(rand(0, 23)),
            ]);
        }
    }
}
