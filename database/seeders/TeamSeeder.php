<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        if ($users->count() < 3) {
            $this->command->warn('Please run UserSeeder first with at least 3 users.');
            return;
        }

        $teams = [
            [
                'name' => 'Frontend Wizards',
                'description' => 'A team dedicated to frontend development, UI/UX, and modern JavaScript frameworks.',
            ],
            [
                'name' => 'Backend Masters',
                'description' => 'Server-side development team focusing on APIs, databases, and microservices.',
            ],
            [
                'name' => 'DevOps Ninjas',
                'description' => 'Infrastructure, CI/CD, and cloud deployment specialists.',
            ],
            [
                'name' => 'Mobile Squad',
                'description' => 'Cross-platform and native mobile app development team.',
            ],
            [
                'name' => 'Open Source Contributors',
                'description' => 'Community-driven team working on open source projects.',
            ],
        ];

        foreach ($teams as $index => $teamData) {
            $owner = $users[$index % $users->count()];

            $team = Team::create([
                'name' => $teamData['name'],
                'description' => $teamData['description'],
                'owner_id' => $owner->id,
                'is_active' => true,
            ]);

            // Add owner as admin member
            $team->members()->attach($owner->id, ['role' => 'admin']);

            // Add random members
            $otherUsers = $users->where('id', '!=', $owner->id)->random(min(3, $users->count() - 1));
            $roles = ['admin', 'member', 'member', 'viewer'];

            foreach ($otherUsers as $memberIndex => $member) {
                $team->members()->attach($member->id, [
                    'role' => $roles[$memberIndex % count($roles)],
                ]);
            }

            // Create pending invitations for some teams
            if ($index < 2) {
                TeamInvitation::create([
                    'team_id' => $team->id,
                    'email' => 'newmember' . $index . '@example.com',
                    'role' => 'member',
                    'token' => Str::random(64),
                    'invited_by' => $owner->id,
                    'expires_at' => now()->addDays(7),
                ]);
            }
        }
    }
}
