<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Run: php artisan db:seed
     * Or fresh migration with seeding: php artisan migrate:fresh --seed
     */
    public function run(): void
    {
        $this->command->info('Starting database seeding...');

        // Core data (required first)
        $this->call([
            LanguageSeeder::class,      // Programming languages
            CategorySeeder::class,      // Snippet categories
            TagSeeder::class,           // Tags for snippets
            UserSeeder::class,          // Users (admin + test users)
        ]);

        // Content data
        $this->call([
            TeamSeeder::class,          // Teams and memberships
            SnippetSeeder::class,       // Code snippets with versions
            CollectionSeeder::class,    // Collections of snippets
            CommentSeeder::class,       // Comments on snippets
        ]);

        // Social/interaction data
        $this->call([
            FollowSeeder::class,        // User follow relationships
            FavoriteSeeder::class,      // Snippet favorites
            ShareSeeder::class,         // Snippet shares
            NotificationSeeder::class,  // User notifications
        ]);

        // System data
        $this->call([
            AuditLogSeeder::class,      // Audit logs
        ]);

        $this->command->info('Database seeding completed!');
        $this->command->newLine();
        $this->command->info('Test credentials:');
        $this->command->info('  Admin: admin@example.com / password');
        $this->command->info('  User:  chhunmengheang5@gmail.com / 12345678');
        $this->command->info('  Other: john@example.com / password (and other test users)');
    }
}
