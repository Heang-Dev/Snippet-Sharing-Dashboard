<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\Snippet;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $snippets = Snippet::with('user')->get();

        if ($users->isEmpty()) {
            $this->command->warn('Please run UserSeeder first.');
            return;
        }

        $notificationTypes = [
            [
                'type' => 'new_follower',
                'title' => 'New Follower',
                'message' => '{actor} started following you',
                'icon' => 'user-plus',
            ],
            [
                'type' => 'snippet_liked',
                'title' => 'Snippet Liked',
                'message' => '{actor} liked your snippet "{snippet}"',
                'icon' => 'heart',
            ],
            [
                'type' => 'new_comment',
                'title' => 'New Comment',
                'message' => '{actor} commented on your snippet "{snippet}"',
                'icon' => 'message-circle',
            ],
            [
                'type' => 'comment_reply',
                'title' => 'Comment Reply',
                'message' => '{actor} replied to your comment',
                'icon' => 'reply',
            ],
            [
                'type' => 'snippet_forked',
                'title' => 'Snippet Forked',
                'message' => '{actor} forked your snippet "{snippet}"',
                'icon' => 'git-branch',
            ],
            [
                'type' => 'team_invitation',
                'title' => 'Team Invitation',
                'message' => 'You have been invited to join {team}',
                'icon' => 'users',
            ],
            [
                'type' => 'mention',
                'title' => 'You were mentioned',
                'message' => '{actor} mentioned you in a comment',
                'icon' => 'at-sign',
            ],
        ];

        foreach ($users as $user) {
            // Create 3-8 notifications per user
            $numNotifications = rand(3, 8);

            for ($i = 0; $i < $numNotifications; $i++) {
                $notifType = $notificationTypes[array_rand($notificationTypes)];
                $actor = $users->where('id', '!=', $user->id)->random();
                $snippet = $snippets->isNotEmpty() ? $snippets->random() : null;

                $message = str_replace(
                    ['{actor}', '{snippet}', '{team}'],
                    [
                        $actor->full_name ?? $actor->username,
                        $snippet?->title ?? 'Unknown Snippet',
                        'Development Team',
                    ],
                    $notifType['message']
                );

                Notification::create([
                    'user_id' => $user->id,
                    'type' => $notifType['type'],
                    'title' => $notifType['title'],
                    'message' => $message,
                    'link' => $snippet ? '/snippets/' . $snippet->slug : '/profile/' . $actor->username,
                    'icon' => $notifType['icon'],
                    'actor_id' => $actor->id,
                    'related_resource_type' => $snippet ? 'snippet' : 'user',
                    'related_resource_id' => $snippet?->id ?? $actor->id,
                    'is_read' => rand(0, 1) === 1,
                    'read_at' => rand(0, 1) === 1 ? now()->subHours(rand(1, 48)) : null,
                    'created_at' => now()->subHours(rand(1, 168)), // Within last week
                ]);
            }
        }
    }
}
