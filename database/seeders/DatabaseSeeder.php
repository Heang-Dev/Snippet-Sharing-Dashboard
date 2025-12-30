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
            'username' => 'mengheang',
            'email' => 'chhunmengheang5@gmail.com',
            'password' => Hash::make('12345678'),
            'full_name' => 'Chhun Mengheang',
            'bio' => 'A test user for development purposes.',
            'is_admin' => false,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
