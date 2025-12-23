<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Seed languages and categories first
        $this->call([
            LanguageSeeder::class,
            CategorySeeder::class,
        ]);

        // Create admin user
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'System Administrator',
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create test user
        User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'full_name' => 'Test User',
            'bio' => 'A test user for development purposes.',
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
