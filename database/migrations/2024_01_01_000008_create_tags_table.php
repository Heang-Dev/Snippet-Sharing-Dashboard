<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();
            $table->string('slug', 60)->unique();
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable();
            $table->integer('usage_count')->default(0);
            $table->boolean('is_official')->default(false);
            $table->foreignUuid('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('slug');
            $table->index('usage_count');
        });

        Schema::create('snippet_tag', function (Blueprint $table) {
            $table->foreignUuid('snippet_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('tag_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['snippet_id', 'tag_id']);
            $table->index('snippet_id');
            $table->index('tag_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('snippet_tag');
        Schema::dropIfExists('tags');
    }
};
