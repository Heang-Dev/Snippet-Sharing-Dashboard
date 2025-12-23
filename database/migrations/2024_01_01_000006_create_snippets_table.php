<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('snippets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('team_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->longText('code');
            $table->longText('highlighted_html')->nullable();
            $table->string('language', 50);
            $table->foreignUuid('category_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('privacy', ['public', 'private', 'team', 'unlisted'])->default('public');
            $table->string('slug', 300)->unique();
            $table->integer('version_number')->default(1);
            $table->foreignUuid('parent_snippet_id')->nullable()->constrained('snippets')->nullOnDelete();
            $table->boolean('is_fork')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->boolean('allow_forks')->default(true);
            $table->string('license', 50)->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('unique_view_count')->default(0);
            $table->integer('fork_count')->default(0);
            $table->integer('favorite_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('share_count')->default(0);
            $table->float('trending_score')->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('team_id');
            $table->index('language');
            $table->index('privacy');
            $table->index('slug');
            $table->index('created_at');
            $table->index('trending_score');
            $table->index('is_featured');
            $table->index('is_fork');
            $table->index('parent_snippet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippets');
    }
};
