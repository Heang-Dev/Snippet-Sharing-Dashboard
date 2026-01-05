<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'username' => 'admin',
                'password' => Hash::make('password'),
                'full_name' => 'System Administrator',
                'bio' => 'Platform administrator with full access.',
                'is_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create test user
        User::firstOrCreate(
            ['email' => 'chhunmengheang5@gmail.com'],
            [
                'username' => 'mengheang',
                'password' => Hash::make('12345678'),
                'full_name' => 'Chhun Mengheang',
                'bio' => 'A passionate developer and code enthusiast.',
                'is_admin' => false,
                'is_active' => true,
                'email_verified_at' => now(),
                'github_url' => 'https://github.com/mengheang',
                'website_url' => 'https://mengheang.dev',
            ]
        );

        // Create additional test users
        $testUsers = [
            [
                'username' => 'johndoe',
                'email' => 'john@example.com',
                'full_name' => 'John Doe',
                'bio' => 'Full-stack developer specializing in React and Node.js',
                'github_url' => 'https://github.com/johndoe',
            ],
            [
                'username' => 'janedoe',
                'email' => 'jane@example.com',
                'full_name' => 'Jane Doe',
                'bio' => 'Backend engineer with expertise in Python and Go',
                'twitter_url' => 'https://twitter.com/janedoe',
            ],
            [
                'username' => 'devmaster',
                'email' => 'devmaster@example.com',
                'full_name' => 'Dev Master',
                'bio' => 'Senior software architect with 10+ years of experience',
            ],
            [
                'username' => 'codeking',
                'email' => 'codeking@example.com',
                'full_name' => 'Code King',
                'bio' => 'Open source contributor and algorithm enthusiast',
            ],
            [
                'username' => 'techguru',
                'email' => 'techguru@example.com',
                'full_name' => 'Tech Guru',
                'bio' => 'DevOps engineer and cloud architecture specialist',
            ],
            [
                'username' => 'webwizard',
                'email' => 'webwizard@example.com',
                'full_name' => 'Web Wizard',
                'bio' => 'Frontend developer focused on UI/UX and accessibility',
            ],
            [
                'username' => 'dataninja',
                'email' => 'dataninja@example.com',
                'full_name' => 'Data Ninja',
                'bio' => 'Data scientist and machine learning practitioner',
            ],
            [
                'username' => 'mobiledev',
                'email' => 'mobiledev@example.com',
                'full_name' => 'Mobile Developer',
                'bio' => 'Android and iOS developer with Flutter expertise',
            ],
        ];

        foreach ($testUsers as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    ...$userData,
                    'password' => Hash::make('password'),
                    'is_admin' => false,
                    'is_active' => true,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
