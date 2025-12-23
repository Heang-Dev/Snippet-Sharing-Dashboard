<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 255);
            $table->string('slug', 300);
            $table->text('description')->nullable();
            $table->string('cover_image_url', 500)->nullable();
            $table->enum('privacy', ['public', 'private', 'unlisted'])->default('public');
            $table->integer('snippet_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['user_id', 'slug']);
            $table->index('user_id');
            $table->index('privacy');
        });

        Schema::create('collection_snippet', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('snippet_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->text('note')->nullable();
            $table->timestamp('added_at')->useCurrent();

            $table->unique(['collection_id', 'snippet_id']);
            $table->index('collection_id');
            $table->index('snippet_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_snippet');
        Schema::dropIfExists('collections');
    }
};
