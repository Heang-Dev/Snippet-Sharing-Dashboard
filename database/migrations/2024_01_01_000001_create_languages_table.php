<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('languages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name', 50)->unique();
            $table->string('slug', 60)->unique();
            $table->string('display_name', 100);
            $table->json('file_extensions'); // [".py", ".pyw"]
            $table->string('pygments_lexer', 100); // python3
            $table->string('monaco_language', 50)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('color', 7)->nullable(); // #3776AB
            $table->integer('snippet_count')->default(0);
            $table->integer('popularity_rank')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('slug');
            $table->index('snippet_count');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('languages');
    }
};
